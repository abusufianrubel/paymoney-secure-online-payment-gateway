<?php

namespace Modules\TatumIo\Services;

use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\TatumIo\Class\TatumIoTransaction;
use Modules\TatumIo\Entities\CryptoToken;
use Modules\TatumIo\Exception\CryptoSendException;

class CryptoSendService
{
    protected $helper;
    protected $currency;
    protected $tatumIo;
    protected $network;


    public function __construct()
    {
        $this->helper = new \App\Http\Helpers\Common();
        $this->currency = new \App\Models\Currency();
    }


    public function cryptoAddressValidation($network, $address, $via='api')
    {
        try {

            $this->setTatumIo($network);
            return $this->tatumIo->checkAddress($address, $via);
            
        } catch (Exception $e) {
           throw new CryptoSendException($e->getMessage());
        }
    }

    public function userCryptoBalanceCheck($network, $amount, $senderAddress, $receiverAddress, $priority='low')
    {
        try {

            $this->getNetworkMinLimit($network, $amount);

            $this->setTatumIo($network);

            $this->checkSenderAddress($senderAddress);

            $this->tatumIo->checkAddress($receiverAddress, 'api');

            return $this->setTransactionArray($senderAddress, $receiverAddress, $amount, $priority);

        } catch (Exception $e) {
            throw new CryptoSendException($e->getMessage());
        }

    }

    
    public function userTokenBalanceCheck($walletId, $walletCurrencyCode, $receiverAddress, $amount)
    {
        try {
            $minNativeBalance = 10;

            $cryptoToken = $this->getUserTokenNetwork(
                $walletId, 
                $walletCurrencyCode
            );

            $minTatumIoLimit = tokenFormat(tokenMinLimit($cryptoToken->decimals), $cryptoToken->decimals);

            if ($amount < $minTatumIoLimit) {
                throw new CryptoSendException(__('The minimum amount must be :x', ['x' => $minTatumIoLimit . ' ' . $walletCurrencyCode]));
            }

            $this->setTatumIo($cryptoToken->network);

            if ($minNativeBalance > $this->tatumIo->getUserBalance()) {
                throw new CryptoSendException(__('Minimum :x amount needed for network fee', ['x' => $minNativeBalance . ' ' . $cryptoToken->network]));
            }

            $tokenBalance = getTokenDecimalBalance(
                $this->tatumIo->getTokenBalanceByAddress(
                    $this->tatumIo->getUserAddress(), 
                    $cryptoToken->address
                ), 
                $cryptoToken->decimals
            );

            if ($amount > $tokenBalance) {
                throw new CryptoSendException(__('Insufficient Token'));
            }

            $this->cryptoAddressValidation($cryptoToken->network, $receiverAddress);

            return $this->setTokenTransactionArray($cryptoToken, $walletCurrencyCode,$receiverAddress, $amount);

        } catch (Exception $e) {
            throw new CryptoSendException($e->getMessage());
        }

    }


    public function getUserTokenNetwork($walletId, $walletCurrencyCode)
    {
        try {
            $wallet = Wallet::find($walletId);
            if ( $wallet) {
                $cryptoToken = CryptoToken::where(
                    ['symbol' => $walletCurrencyCode, 
                    'currency_id' => $wallet->currency_id ]
                    )->first();
                if ($cryptoToken) {
                    return $cryptoToken;
                }
            }
            throw new CryptoSendException(__('Token info not Valid'));

        } catch (Exception $e) {
            throw new CryptoSendException($e->getMessage());
        }
        
    }

    public function sendTokenFinal($network, $walletCurrencyCode, $walletId, $receiverAddress, $amount)
    {
        try {

            $this->setTatumIo($network);

            $cryptoTrx = $this->userTokenBalanceCheck(
                $walletId, 
                $walletCurrencyCode, 
                $receiverAddress, 
                $amount
            );

            $sendResponse = $this->tatumIo->sendTokenToAddress(
                $cryptoTrx['tokenAddress'], 
                $amount, 
                $receiverAddress
            );

            if (!isset($sendResponse->txId)) {
                $message = isset($sendResponse->cause) ?  $sendResponse->cause : __('Transaction Failed, please try again');
                throw new CryptoSendException(__($message));
            }

            DB::beginTransaction();


            $createCryptoTransactionId = $this->tatumIo->createTokenTransaction($cryptoTrx);

            $withdrawInfoResponse = $this->tatumIo->getTokenTransactionDetails($sendResponse->txId);

            Log::info(json_encode($withdrawInfoResponse));

            $cryptoTrx['transactionId'] = $createCryptoTransactionId;

            $withdrawInfoData = [
                'txId' => isset($withdrawInfoResponse->txID) ? $withdrawInfoResponse->txID : null ,
                'senderAddress' => $this->tatumIo->getUserAddress(),
                'receiverAddress' => $receiverAddress,
                'network_fee' => isset($withdrawInfoResponse->fee)? getTokenDecimalBalance($withdrawInfoResponse->fee, $cryptoTrx['tokenDecimals']) : 0,
                'token' => $walletCurrencyCode,
                'network' => $network
            ];

            $cryptoTrx['withdrawInfoData'] = $withdrawInfoData;
          
            $this->tatumIo->createWithdrawalOrSendTokenApiLog($cryptoTrx);

            $wallet = Wallet::find($walletId);
            $wallet->balance = $wallet->balance - $amount;
            $wallet->save();

            DB::commit();

            return $cryptoTrx;

        } catch (Exception $e) {
            DB::rollBack();
            throw new CryptoSendException(__($e->getMessage()));
        }

    }




    public function userAddress($network)
    {
        try {

            $this->setTatumIo($network);

            return [
                'senderAddress' => $this->tatumIo->getUserAddress()
            ];
        } catch (Exception $e) {
           throw new CryptoSendException($e->getMessage());
        }

    }

    public function sendCryptoFinal($network, $receiverAddress, $amount, $priority, $senderAddress)
    {
        try {

            $this->setTatumIo($network);

            $this->checkSenderAddress($senderAddress);

            $this->tatumIo->checkAddress($receiverAddress, 'api');


            $cryptoTrx =  $this->setTransactionArray($senderAddress, $receiverAddress, $amount, $priority);

            $cryptoTrx['uniqueCode'] = unique_code();


            $sendResponse = $this->tatumIo->sendCryptoToAddress($receiverAddress, $amount, $priority);

            if (!isset($sendResponse->txId)) {

                $message = isset($sendResponse->cause) ?  $sendResponse->cause : __('Transaction Failed, please try again');
                throw new Exception(__($message));
            }


            DB::beginTransaction();

            $createCryptoTransactionId = $this->tatumIo->createCryptoTransaction($cryptoTrx);

            $cryptoTrx['transactionId'] = $createCryptoTransactionId;
            $cryptoTrx['withdrawInfoData'] = $sendResponse;


            //Create new withdrawal/Send crypt api log
            $cryptoTrx['transactionId'] = $createCryptoTransactionId;
            $cryptoTrx['walletCurrencyCode'] = $network;

            //need this for showing send address against Crypto Receive Type Transaction in user/admin panel
            $cryptoTrx['withdrawInfoData']->network_fee = $cryptoTrx['networkFee'];

            $cryptoTrx['withdrawInfoData']->senderAddress = $senderAddress;
            //need this for nodejs websocket server
            $cryptoTrx['withdrawInfoData']->receiverAddress = $cryptoTrx['receiverAddress'];

            $this->tatumIo->createWithdrawalOrSendCryptoApiLog($cryptoTrx);

            $this->tatumIo->getUpdatedSendWalletBalance($cryptoTrx);

            DB::commit();

            return $cryptoTrx;

        } catch (Exception $e) {
            DB::rollBack();
            throw new CryptoSendException(__($e->getMessage()));
        }

    }


    public function getNetworkMinLimit($network, $amount)
    {
        $minLimit =  getTatumIoMinLimit('amount', $network);

        if ($minLimit > $amount) {
            throw new CryptoSendException(__('The minimum amount must be :x', ['x' => $minLimit . ' ' .$network]));
        }
        return true;
    }

    public function getCryptoCurrency( $options = ['id', 'symbol', 'status'])
    {
        $currency = $this->currency->getCurrency(['code' => $this->network, 'type' => 'crypto_asset'], $options);

        if ($currency->status !== 'Active') {
            throw new CryptoSendException(__(':x is inactive.', ['x' =>  $this->network]));
        }
        return $currency;
    }

    public function setTatumIo($network)
    {
        $userId = auth()->id();

        $this->network = strtoupper($network);

        $this->tatumIo = new TatumIoTransaction($this->network);

        $this->tatumIo->tatumIoAsset();

        $this->tatumIo->checkUserTatumWallet($userId);

    }

    public function checkSenderAddress($address)
    {
        if ( $address !== $this->tatumIo->getUserAddress() ) {
            throw new CryptoSendException(__('Sender Address is not correct'));
        }

        return true;

    }

    public function setTransactionArray($senderAddress, $receiverAddress, $amount, $priority)
    {

        $currency = $this->getCryptoCurrency();


        $networkFees = $this->tatumIo->getEstimatedFees($senderAddress, $receiverAddress, $amount, $priority);

        $userBalance = $this->tatumIo->getUserBalance();

        if ($userBalance < ($amount + $networkFees)) {
            throw new Exception(__('Network fee :x and Amount :y exceeds your :z balance', ['x' => $networkFees, 'y' => $amount, 'z' => strtoupper($this->network), 'b' => $userBalance]));
        }

        if ($senderAddress == $receiverAddress) {
           throw new Exception(__('You can not send :x to your own wallet', ['x' => $this->network]));
        }

        $arr = [
            'receiverAddress' => $receiverAddress,
            'amount' => $amount,
            'networkFee' => $networkFees,
            'senderAddress' => $senderAddress,
            'userId' => auth()->id(),
            'currencyId' => $currency->id,
            'currencySymbol' => $currency->symbol,
            'priority' => $priority,
            'network' => $this->network,
        ];

        $endUserWallet = getReceiverAddressWalletUserId($receiverAddress);

        if (!empty($endUserWallet)) {
            $arr['endUserId'] = optional($endUserWallet->wallet)->user_id;
        } else {
            $arr['endUserId'] = null;
        }

        return $arr;

    }

    public function setTokenTransactionArray($cryptoToken, $walletCurrencyCode,$receiverAddress, $amount)
    {

        $endUserWallet = getReceiverAddressWalletUserId($receiverAddress);
    
        return [
            'walletCurrencyCode' => $walletCurrencyCode,
            'network' => $this->network,
            'amount' => $amount,
            'networkFee' => 10,
            'userId' => auth()->id(),
            'endUserId' => (!empty($endUserWallet)) ?  optional($endUserWallet->wallet)->user_id : null,
            'currencyId' => $cryptoToken->currency_id,
            'currencySymbol' =>  $cryptoToken->symbol,
            'senderAddress' => $this->tatumIo->getUserAddress(),
            'receiverAddress' => $receiverAddress,
            'tokenAddress' => $cryptoToken->address,
            'tokenDecimals' => $cryptoToken->decimals,
            'transactionType' => Token_Sent,
            'object_type' => 'token_sent',
            'uniqueCode' => unique_code(),
            'confirmation' => 0,
            'status' => 'Pending',
        ];
    }



}
