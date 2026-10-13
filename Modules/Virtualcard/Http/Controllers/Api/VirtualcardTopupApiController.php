<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use Exception;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Events\TopupRequest;
use Modules\Virtualcard\Entities\Virtualcard;
use Modules\Virtualcard\Services\VirtualcardTopupService;
use Modules\Virtualcard\Http\Requests\User\ValidateTopupRequest;

class VirtualcardTopupApiController extends Controller
{
    protected $helper;
    protected $service;

    public function __construct(VirtualcardTopupService $service)
    {
        $this->helper  = new Common();
        $this->service  = $service;
    }

    public function virtualcard(Request $request)
    {
        try {
            if (empty($request->virtualcardId)) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }

            $virtualcard = $this->service->getUserSingleVirtualCard($request->virtualcardId);
            if (empty($virtualcard)) {
                return $this->notFoundResponse(__("The virtual card is not available for use."));
            }
            return $this->successResponse($virtualcard);
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

    public function getTopUpFeesLimit(Request $request)
    {
        try {
            $feesLimit = $this->service->validateTopupFeesLimit($request->amount, $request->virtualCardId, $request->topupWallet);
            return $this->successResponse(['feesLimit' => $feesLimit]);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function topupConfirm(ValidateTopupRequest $request)
    {
        try {
            $transInfo  = $this->service->validateTopupFeesLimit(
                $request->amount,
                $request->virtualCardId,
                $request->topupWallet
            );
            return $this->successResponse($transInfo);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function topupSuccess(Request $request)
    {
        if (empty($request)) {
            return $this->notFoundResponse(__("Transaction data not found"));
        }

        try {

            $transInfo  = $this->service->validateTopupFeesLimit(
                $request->amount,
                $request->virtualCardId,
                $request->currencyCode
            );
            $transInfo = $this->service->virtualcardTopupTransaction($request);
            event(new TopupRequest($transInfo['topup']));
            return $this->successResponse($transInfo);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

}
