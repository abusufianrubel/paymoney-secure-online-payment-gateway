<?php

namespace Modules\Virtualcard\Http\Controllers\User;

use App\Models\{
    Wallet,
    Currency
};
use Spatie\QueryBuilder\{
    QueryBuilder,
    AllowedFilter
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardWithdrawal
};
use Modules\Virtualcard\{
    Events\WithdrawalRequest,
    Actions\CreateVirtualcardWithdrawalAction,
    DataTransferObject\VirtualcardWithdrawaData,
    Http\Requests\User\CreateVirtualcardWithdrawalRequest
};

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon, Exception, DB, Common;


class VirtualcardWithdrawalController extends Controller
{
    public function __construct(

        private readonly CreateVirtualcardWithdrawalAction $createVirtualcardWithdrawalAction

    ) {}

    public function index()
    {
        $virtualcardWithdrawals = QueryBuilder::for(VirtualcardWithdrawal::class)
                                        ->allowedFilters([
                                            AllowedFilter::exact('status', 'fund_approval_status'),
                                            AllowedFilter::callback('currency', function ($query, $value) {
                                                $query->whereHas('virtualcard', function ($subQuery) use ($value) {
                                                    $subQuery->where('currency_code', $value);
                                                });
                                            }),
                                            AllowedFilter::callback('from', function ($query, $value) {
                                                $query->where('fund_request_time', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
                                            }),
                                            AllowedFilter::callback('to', function ($query, $value) {
                                                $query->where('fund_request_time', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
                                            }),
                                            AllowedFilter::callback('brand', function ($query, $value) {
                                                $query->whereHas('virtualcard', function ($subQuery) use ($value) {
                                                    $subQuery->where('card_brand', $value);
                                                });
                                            }),
                                        ])
                                        ->where('user_id', auth()->id())
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $virtualcards = Virtualcard::with(['virtualcardHolder', 'virtualcardHolder.user', 'virtualcardProvider', 'virtualcardProvider.feesLimit'])
                            ->whereHas( 'virtualcardHolder.user', function($query) {
                                $query->where('user_id', auth()->id());
                            })
                            ->whereHas('virtualcardProvider')
                            ->whereHas('virtualcardProvider.feesLimit')
                            ->where(function ($query) use ($currentYear, $currentMonth) {
                                $query->where('expiry_year', '>', $currentYear)
                                    ->orWhere(function ($subQuery) use ($currentYear, $currentMonth) {
                                        $subQuery->where('expiry_year', '=', $currentYear)
                                        ->where('expiry_month', '>=', $currentMonth);
                                    });
                                })
                            ->whereStatus('Active')
                            ->get();


        return view('virtualcard::user.virtualcard_withdrawals.index', [

            'statuses' => VirtualcardWithdrawal::select('fund_approval_status')->where('user_id', auth()->id())->distinct()->get(),
            'currencies' =>  VirtualcardWithdrawal::select('virtualcards.currency_code')->distinct()->join('virtualcards', 'virtualcard_withdrawals.virtualcard_id', '=', 'virtualcards.id')->get(),
            'virtualcardWithdrawals' => $virtualcardWithdrawals,
            'brands' => Virtualcard::select('card_brand')->distinct()->get(),
            'filter' => request()->input('filter'),
            'virtualcards' => $virtualcards

        ]);
    }

    public function create()
    {
        if (route('user.virtualcard_withdrawal.confirm') !== url()->previous()) {
            if (!empty(session('paymentData'))) {
                session()->forget('paymentData');
            }
        }

        $cardId = request()->card_id;
        $currentYear    = Carbon::now()->year;
        $currentMonth   = Carbon::now()->month;

        if ($cardId) {

            $data['virtualcard'] = Virtualcard::with(['virtualcardHolder', 'virtualcardHolder.user'])
            ->whereHas( 'virtualcardHolder.user', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->where([['id', '=', $cardId]])
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('expiry_year', '>', $currentYear)
                    ->orWhere(function ($subQuery) use ($currentYear, $currentMonth) {
                        $subQuery->where('expiry_year', '=', $currentYear)
                        ->where('expiry_month', '>=', $currentMonth);
                    });
                })
            ->whereStatus('Active')
            ->first();

            if (empty($data['virtualcard'])) {
                Common::one_time_message('error', __('The virtual card is not available for use.'));
                return redirect()->route('user.virtualcard.index');
            }
            return view('virtualcard::user.virtualcard_withdrawals.create_single', $data);
        }

        $data['virtualcards'] = Virtualcard::with(['virtualcardHolder', 'virtualcardHolder.user', 'virtualcardProvider', 'virtualcardProvider.feesLimit'])
                                ->whereHas( 'virtualcardHolder.user', function($query) {
                                    $query->where('user_id', auth()->id());
                                })
                                ->whereHas('virtualcardProvider')
                                ->whereHas('virtualcardProvider.feesLimit')
                                ->where(function ($query) use ($currentYear, $currentMonth) {
                                    $query->where('expiry_year', '>', $currentYear)
                                        ->orWhere(function ($subQuery) use ($currentYear, $currentMonth) {
                                            $subQuery->where('expiry_year', '=', $currentYear)
                                            ->where('expiry_month', '>=', $currentMonth);
                                        });
                                    })
                                ->whereStatus('Active')
                                ->get();


        return view('virtualcard::user.virtualcard_withdrawals.create', $data);
    }

    public function confirm(CreateVirtualcardWithdrawalRequest $request)
    {
        try {

            $data['virtualcardWithdrawalData'] = $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);
            $data['virtualcardWithdrawalFee'] = $this->createVirtualcardWithdrawalAction->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);

            setPaymentData(['virtualcardId' => $virtualcardWithdrawalData->virtualcard?->id, 'walletId' =>$virtualcardWithdrawalData->wallet?->id, 'amount' => $virtualcardWithdrawalData->requestedFund, 'virtualcardWithdrawalData' => $virtualcardWithdrawalData]);

            return view('virtualcard::user.virtualcard_withdrawals.confirm', $data);

        } catch (Exception $e) {

            return redirect()
                ->route('user.virtualcard_withdrawal.create')
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function success()
    {
        try {

            DB::beginTransaction();

            $virtualcardWithdrawalData = session()->get('paymentData');
            $withdrawalData = $this->createVirtualcardWithdrawalAction->execute($virtualcardWithdrawalData['virtualcardWithdrawalData']);
            event(new WithdrawalRequest($withdrawalData));
            DB::commit();

            return view('virtualcard::user.virtualcard_withdrawals.success', [
                'virtualcardWithdrawalData' => $virtualcardWithdrawalData,
                'withdrawalData' => $withdrawalData
            ]);

        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function wallet(Request $request)
    {
        $virtualcard = Virtualcard::find($request->virtualcardId, ['id', 'currency_code']);

        if ($virtualcard) {
            $currency = Currency::where('code', $virtualcard->currency_code)->first();

            $wallet = Wallet::with('currency:id,code,type')->where(['currency_id' => $currency->id, 'user_id' => auth()->id()])->first();

            if ($wallet) {
                $data[] = [
                    'wallet_id' => $wallet->id,
                    'code' => $wallet->currency?->code . ' ' . __('Wallet'),
                    'type' => $wallet->currency?->type
                ];
            }

            return response()->json([
                'wallets' => $data
            ]);
        }

    }

    public function retrieveFeesLimit(CreateVirtualcardWithdrawalRequest $request)
    {
        try {
            $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);

            $fees = $this->createVirtualcardWithdrawalAction->withdrawalFee(
                $virtualcardWithdrawalData->virtualcard?->virtualcardProvider,
                $virtualcardWithdrawalData->virtualcard?->currency_code,
                $virtualcardWithdrawalData->requestedFund,
            );

            $fixedFee = $virtualcardWithdrawalData->requestedFund > 0 ? $fees->fixedFee : 0;
            $totalFee = $virtualcardWithdrawalData->requestedFund > 0 ? $fees->totalFee : 0;


            $data['response'] = [
                'feesFixed'                 => $fees->fixedFee,
                'feesPercentage'            => $fees->percentageFee,
                'totalFees'                 => $fees->totalFee,
                'amount'                    => $virtualcardWithdrawalData->requestedFund,
                'totalAmount'               => $virtualcardWithdrawalData->requestedFund + $fees->totalFee,
                'formattedFeesFixed'        => formatNumber($fixedFee, $virtualcardWithdrawalData->wallet?->currency_id),
                'formattedFeesPercentage'   => formatNumber($fees->percentageFee, $virtualcardWithdrawalData->wallet?->currency_id) . '%',
                'formattedTotalFees'        => formatNumber($totalFee, $virtualcardWithdrawalData->wallet?->currency_id),
                'formattedAmount'           => formatNumber($virtualcardWithdrawalData->requestedFund, $virtualcardWithdrawalData->wallet?->currency_id),
                'formattedTotalAmount'      => formatNumber($virtualcardWithdrawalData->requestedFund + $fees->totalFee, $virtualcardWithdrawalData->wallet?->currency_id),
                'currencyId'                => $virtualcardWithdrawalData->wallet?->currency_id,
                'currencyType'              => $virtualcardWithdrawalData->wallet?->currency?->type,
                'currencyCode'              => $virtualcardWithdrawalData->wallet?->currency?->code,
                'currencySymbol'            => $virtualcardWithdrawalData->wallet?->currency?->symbol
            ];


            $data['status'] = '200';

            return response()->json(['success' => $data]);

        } catch (Exception $e) {

            $data = [
                'message' => $e->getMessage(),
                'status' => '401'
            ];
        }
    }

    public function validateAmountLimit(CreateVirtualcardWithdrawalRequest $request)
    {
        try {
            $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);
            $virtualcardWithdrawalFee = $this->createVirtualcardWithdrawalAction->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);
            return response()->json(['status' => '200']);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => '401'
            ]);
        }
    }

    public function pdf(VirtualcardWithdrawal $virtualcardWithdrawal)
    {
        $data['transactionDetails'] = $virtualcardWithdrawal->load([
            'user:id,first_name,last_name',
            'transaction:id,transaction_reference_id,transaction_type_id,uuid,currency_id,total,charge_percentage,charge_fixed',
            'transaction.currency:id,symbol,code'
        ]);

        generatePDF('virtualcard::user.virtualcard_withdrawals.pdf', 'virtualcard_withdrawals_', $data);
    }
}
