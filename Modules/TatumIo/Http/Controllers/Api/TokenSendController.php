<?php

/**
 * @package TokenSendController
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Ashraful Alam <[ashraful.techvill@gmail.com]>
 * @created 27-28-2024
 */

namespace Modules\TatumIo\Http\Controllers\Api;


use Exception;
use App\Http\Controllers\Controller;
use Modules\TatumIo\Exception\CryptoSendException;
use Modules\TatumIo\Http\Requests\TokenSendRequest;
use Modules\TatumIo\Services\CryptoSendService;

class TokenSendController extends Controller
{

    protected $service;

    public function __construct(CryptoSendService $service)
    {
        $this->service = $service;
    }

    public function userCryptoAddress()
    {
        try {
            $cryptoToken = $this->service->getUserTokenNetwork(
                request('walletId'),
                request('walletCurrencyCode')
            );
            $network =  $cryptoToken->network;
            $response = $this->service->userAddress($network);

            $data = [
                'network' => $network,
                'cryptoToken' => $cryptoToken->symbol,
                'cryptoAddress' =>  $response['senderAddress'],
                'minTatumIoLimit' => tokenFormat(tokenMinLimit($cryptoToken->decimals), $cryptoToken->decimals)
            ];

            return $this->successResponse($data);
        } catch (CryptoSendException $e) {
            return $this->unprocessableResponse([], ($e->getMessage()));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }


    public function validateCryptoAddress()
    {
        try {
            $response =  $this->service->cryptoAddressValidation(
                request('network'),
                request('address')
            );
            return $this->successResponse($response);
        } catch (CryptoSendException $e) {
            return $this->unprocessableResponse([], __($e->getMessage()));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }

    /**
     * Method validateUserBalanceAgainstAmount
     *
     * @return void
     */
    public function validateUserBalanceAgainstAmount()
    {
        try {
            extract(request()->only(['walletId', 'walletCurrencyCode', 'receiverAddress', 'amount']));
            $response = $this->service->userTokenBalanceCheck(
                $walletId,
                $walletCurrencyCode,
                $receiverAddress,
                $amount
            );

            return $this->successResponse($response);
        } catch (CryptoSendException $e) {
            return $this->unprocessableResponse([], __($e->getMessage()));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }


    public function cryptoSendConfirm(TokenSendRequest $request)
    {
        try {
            extract($request->only(['network', 'walletCurrencyCode', 'walletId', 'receiverAddress', 'amount']));

            $response = $this->service->sendTokenFinal(
                $network,
                $walletCurrencyCode,
                $walletId,
                $receiverAddress,
                $amount
            );

            return $this->successResponse($response);
        } catch (CryptoSendException $e) {
            return $this->unprocessableResponse([], __($e->getMessage()));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], __("Failed to process the request."));
        }
    }
}
