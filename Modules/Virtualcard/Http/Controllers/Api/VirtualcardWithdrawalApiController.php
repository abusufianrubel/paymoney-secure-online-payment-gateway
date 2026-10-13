<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Virtualcard\Entities\Virtualcard;
use Modules\Virtualcard\Events\WithdrawalRequest;
use Modules\Virtualcard\Actions\CreateVirtualcardWithdrawalAction;
use Modules\Virtualcard\DataTransferObject\VirtualcardWithdrawaData;
use Modules\Virtualcard\Http\Requests\User\CreateVirtualcardWithdrawalRequest;

class VirtualcardWithdrawalApiController extends Controller
{
    public function __construct(

        private readonly CreateVirtualcardWithdrawalAction $createVirtualcardWithdrawalAction

    ) {}


    public function virtualcard(Request $request)
    {
        try {
            if (empty($request->virtualcardId)) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }
            $currentYear    = Carbon::now()->year;
            $currentMonth   = Carbon::now()->month;
            $virtualCard = Virtualcard::with(['virtualcardHolder', 'virtualcardHolder.user'])
            ->whereHas( 'virtualcardHolder.user', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('expiry_year', '>', $currentYear)
                    ->orWhere(function ($subQuery) use ($currentYear, $currentMonth) {
                        $subQuery->where('expiry_year', '=', $currentYear)
                        ->where('expiry_month', '>=', $currentMonth);
                    });
                })
            ->whereStatus('Active')
            ->first();

            if (empty($virtualCard)) {
                return $this->notFoundResponse(__("The virtual card is not available for use."));
            }
            $cardLength       = strlen($virtualCard->card_number);
            $maskedCardNumber = str_repeat('*', $cardLength - 4) . substr($virtualCard->card_number, -4);

            return $this->successResponse([
                'virtualcardId' => $virtualCard->id,
                'wallet'   => $virtualCard->currency_code,
                'cardName' => "{$virtualCard->virtualcardHolder?->business_name} ({$maskedCardNumber})"
            ]);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }


    public function wallets(Request $request)
    {
        $virtualcard = Virtualcard::find($request->virtualcardId, ['id', 'currency_code']);

        if (empty($virtualcard)) {
            return $this->notFoundResponse(__("The virtual card is not available for use."));
        }
        $currency = Currency::where('code', $virtualcard->currency_code)->first();
        $wallet = Wallet::with('currency:id,code,type')->where(['currency_id' => $currency->id, 'user_id' => auth()->id()])->first();

        if (empty($wallet)) {
            return $this->notFoundResponse(__("Wallet not found"));
        }
        return $this->successResponse([
            'walletId' => $wallet->id,
            'code' => $wallet->currency?->code . ' ' . __('Wallet'),
            'type' => $wallet->currency?->type
        ]);
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


            $data = [
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
            return $this->successResponse(['feesLimit' => $data]);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function validateAmountLimit(CreateVirtualcardWithdrawalRequest $request)
    {
        try {
            $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);
            $virtualcardWithdrawalFee = $this->createVirtualcardWithdrawalAction->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);
            return $this->successResponse(['status' => '200']);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function confirm(CreateVirtualcardWithdrawalRequest $request)
    {
        try {
            $data['virtualcardWithdrawalData'] = $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);
            $data['virtualcardWithdrawalFee'] = $this->createVirtualcardWithdrawalAction->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);

            return $this->successResponse($data);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function success(CreateVirtualcardWithdrawalRequest $request)
    {
        try {
            $virtualcardWithdrawalData = VirtualcardWithdrawaData::fromRequest($request);
            $this->createVirtualcardWithdrawalAction->checkVirtualcardWithdrawalDataValidity($virtualcardWithdrawalData);
            DB::beginTransaction();
            $withdrawalData = $this->createVirtualcardWithdrawalAction->execute($virtualcardWithdrawalData);
            event(new WithdrawalRequest($withdrawalData));
            DB::commit();

            return $this->successResponse([
                'virtualcardWithdrawalData' => $virtualcardWithdrawalData,
                'withdrawalData' => $withdrawalData
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

}
