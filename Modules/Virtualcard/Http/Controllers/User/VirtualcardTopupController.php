<?php

namespace Modules\Virtualcard\Http\Controllers\User;

use Spatie\QueryBuilder\{
    AllowedFilter,
    QueryBuilder
};
use Modules\Virtualcard\Entities\{
    Virtualcard,
    VirtualcardTopup
};
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Virtualcard\{
    Events\TopupRequest,
    Services\VirtualcardTopupService,
    Http\Requests\User\ValidateTopupRequest
};

class VirtualcardTopupController extends Controller
{
    protected $helper;
    protected $service;

    public function __construct(VirtualcardTopupService $service)
    {
        $this->helper  = new Common();
        $this->service  = $service;
    }

    public function index()
    {
        $virtualcardTopups = QueryBuilder::for(VirtualcardTopup::with([
                                'virtualcard',
                                'transaction:uuid,transaction_reference_id,transaction_type_id',
                                'virtualcard.virtualcardProvider'
                            ]))->where('user_id', auth()->id())
                            ->allowedFilters([
                                AllowedFilter::exact('status', 'fund_approval_status'),
                                AllowedFilter::callback('from', function ($query, $value) {
                                    $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
                                }),
                                AllowedFilter::callback('to', function ($query, $value) {
                                    $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
                                }),
                                AllowedFilter::callback('currency', function ($query, $value) {
                                    $query->whereHas('virtualcard', function ($subQuery) use ($value) {
                                        $subQuery->where('currency_code', $value);
                                    });
                                }),
                                AllowedFilter::callback('brand', function ($query, $value) {
                                    $query->whereHas('virtualcard', function ($subQuery) use ($value) {
                                        $subQuery->where('card_brand', $value);
                                    });
                                }),
                            ])
                            ->orderBy('fund_request_time', 'desc')
                            ->paginate(10);

        return view('virtualcard::user.topups.index', [

            'virtualcardTopups' => $virtualcardTopups,
            'virtualcardTopupStatuses' => $this->service->getDistinctValues('fund_approval_status'),
            'virtualcardCurrencies' => $this->service->getDistinctCardValues('currency_code'),
            'virtualcardBrands' => $this->service->getDistinctCardValues('card_brand'),
            'filter' => request()->input('filter'),
            'virtualCards' => $this->service->getUserVirtualCards()

        ]);
    }

    public function create()
    {

        $data = [
            'menu' => 'virtualcard',
            'content_title' => 'virtualcard'
        ];
        setActionSession();

        if (route('user.topup.confirm') !== url()->previous()) {
            if (!empty(session('paymentData'))) {
                session()->forget('paymentData');
            }
        }

        $cardId = request()->card_id;

        if ($cardId) {
            $data['singleVirtualCard'] = $this->service->getUserSingleVirtualCard($cardId);

            if (empty($data['singleVirtualCard'])) {
                Common::one_time_message('error', __('The virtual card is not available for use.'));
                return redirect()->route('user.virtualcard.index');
            }
            return view('virtualcard::user.topups.create_single', $data);
        } else {
            $data['virtualCards'] = $this->service->getUserVirtualCards();
        }

        return view('virtualcard::user.topups.create', $data);
    }

    public function getTopupWaallets(Request $request)
    {
        return response()->json([
            'success' => Virtualcard::where('id', $request->virtualCard)->value('currency_code')
        ]);
    }

    public function getTopUpFeesLimit(Request $request)
    {
        try {

            $data = $this->service->validateTopupFeesLimit($request->amount, $request->virtualCard, $request->topup_wallet);
            $data['status'] = '200';

        } catch (Exception $e) {
            $data = [
                'message' => __($e->getMessage()),
                'status' => '401'
            ];
        }
        return response()->json(['success' => $data]);
    }

    public function topupConfirm(ValidateTopupRequest $request)
    {
        try {
            $transInfo  = $this->service->validateTopupFeesLimit(
                $request->amount,
                $request->virtualCardId,
                $request->topupWallet
            );

            setPaymentData($transInfo);
            return view('virtualcard::user.topups.confirm', $transInfo);

        } catch (Exception $e) {
            $this->helper->one_time_message('error', __( $e->getMessage() ));
            return redirect()->route('user.topup.create');
        }

    }

    public function topupSuccess()
    {
        $sessionValue = session('paymentData');
        if (empty($sessionValue)) {
            $this->helper->one_time_message('error', __('Transaction data not found'));
            return redirect()->route('user.topup.create');
        }
        actionSessionCheck();

        try {

            $transInfo = $this->service->virtualcardTopupTransaction($sessionValue);
            event(new TopupRequest($transInfo['topup']));
            Session::forget('paymentData');
            clearActionSession();
            return view('virtualcard::user.topups.success', $transInfo);

        } catch (Exception $e) {

            Session::forget('paymentData');
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect()->route('user.topup.create');

        }
    }

    public function topupPrintPdf($topupId)
    {
        $data['transactionDetails'] = VirtualcardTopup::with([
            'user:id,first_name,last_name',
            'transaction:id,transaction_reference_id,transaction_type_id,uuid,currency_id,total,charge_percentage,charge_fixed',
            'transaction.currency:id,symbol,code'
        ])->find($topupId);
        generatePDF('virtualcard::user.topups.pdf', 'topup_', $data);
    }

}
