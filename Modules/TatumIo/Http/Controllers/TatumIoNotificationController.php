<?php

namespace Modules\TatumIo\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Transaction,
    Wallet,
    CryptoAssetApiLog
};
use Exception;
use Modules\TatumIo\Class\TatumIoTransaction;
use Modules\TatumIo\Entities\CryptoToken;

class TatumIoNotificationController extends Controller
{
    protected $tatumIo;
    protected $data;
    protected $transactionDetails;
    protected $network;

    public function balanceNotification(Request $request)
    {
        \Log::info($request->all());

        try {

            $jsonData = $request->getContent();

            $this->data = json_decode($jsonData);

            $apiLog = tatumGetCryptoTransactionApiLog($this->data?->txId);

            \Log::info($apiLog);

            $this->userCryptoWalletUpdate($this->data?->address);
            
            if (isset($this->data?->counterAddress)) {
                $this->userCryptoWalletUpdate($this->data?->counterAddress);
            }
            
            $this->cryptoReceivedUpdateLog($apiLog);
            

            if (!$apiLog) {
                $this->cryptoReceivedAnonymous();
            }

            if ($this->data->type == 'trc20') {

                $this->tokenWalletUpdate($this->data?->txId, $this->data?->address);
                $this->tokenWalletUpdate($this->data?->txId, $this->data?->counterAddress);
            }

        } catch (Exception $th) {
            \Log::info($th->getMessage().$th->getLine());
        }
    }

    private function userCryptoWalletUpdate($address)
    {
        $tatumWalletApiLog = tatumGetWalletApiLog($address);
        
        if (!empty($tatumWalletApiLog)) {
            
            $this->network = $tatumWalletApiLog->network;

            $this->tatumIo = new TatumIoTransaction($tatumWalletApiLog->network);
            
            $this->tatumIo->tatumIoAsset();
            
            $this->tatumIo->checkUserTatumWallet(optional($tatumWalletApiLog->wallet)->user_id);

            $balance = $this->tatumIo->getAddressBalance($address);
            
            Wallet::where(
                [
                    'user_id' => optional($tatumWalletApiLog->wallet)->user_id,
                    'currency_id' => optional($tatumWalletApiLog->wallet)->currency_id
                ]
            )->update(['balance' => $balance]);
        }

        return $tatumWalletApiLog;
    }

    private function cryptoReceivedUpdateLog($apiLog)
    {

        if (!empty($apiLog) && ($apiLog->confirmations == 0) ) {

            if (str_contains($apiLog->object_type, 'sent')) {
                $receiveObjectType = str_replace('sent', 'received', $apiLog->object_type);
            } elseif (str_contains($apiLog->object_type, 'received')) {
                $receiveObjectType = str_replace('received', 'sent', $apiLog->object_type);
            }

            $trnsactionType = str_replace(' ', '_', ucwords(str_replace('_', ' ', $apiLog->object_type)));
            $receiveTransaction = str_replace(' ', '_', ucwords(str_replace('_', ' ', $receiveObjectType)));

            $transaction = Transaction::where([
                'id' => optional($apiLog->transaction)->id,
                'transaction_type_id' => constant($trnsactionType),
                'status' => 'Pending']
            )->first();
            
            if (!empty($transaction) && ($transaction->status == 'Pending')) {

                $this->tatumIo = new TatumIoTransaction($this->network);

                $this->tatumIo->tatumIoAsset();

                $this->transactionDetails = $this->tatumIo->transactionView($this->data?->txId);
                
                $apiLog->confirmations = 7;

                $payload = json_decode($apiLog->payload);

                $payload->network_fee = $this->transactionDetails['networkFee'] ??  $payload->network_fee;

                $apiLog->payload = json_encode($payload);
                $apiLog->save();

                $transaction->status = 'Success';
                $transaction->save();


                $payload2['txId'] = $payload->txId;
                $payload2['senderAddress'] = $payload->senderAddress;
                $payload2['receiverAddress'] = $payload->receiverAddress;
                
                if (isset($payload->network)) {
                    $payload2['network'] = $payload->network;
                }
                
                if (isset($payload->senderAddress)) {
                    $this->userCryptoWalletUpdate($payload->senderAddress);
                }
                
                
                if (isset($payload->receiverAddress)) {
                    $this->userCryptoWalletUpdate($payload->receiverAddress);
                }

                \Log::info('ok');
                


                if ($transaction->end_user_id ) {
                    
                    $cryptoReceiveTransaction = Transaction::where([
                        'uuid' =>  $transaction->uuid,
                        'payment_method_id' => TatumIo,
                        'transaction_type_id' => constant($receiveTransaction)
                    ])->first();
                    
                    $transactionB = ($cryptoReceiveTransaction) ? $cryptoReceiveTransaction : new Transaction() ;
                    $transactionB->user_id = $transaction->end_user_id;
                    $transactionB->end_user_id = $transaction->user_id;
                    $transactionB->currency_id = $transaction->currency_id;
                    $transactionB->transaction_type_id = constant($receiveTransaction);
                    $transactionB->subtotal = $transaction->subtotal;
                    $transactionB->total = $transaction->subtotal;
                    $transactionB->uuid = $transaction->uuid;
                    $transactionB->payment_method_id = TatumIo;
                    $transactionB->status = 'Success';
                    $transactionB->save();

                    $cryptoReceiveApiLog  = (new CryptoAssetApiLog())
                        ->where(['payment_method_id' => TatumIo, 'object_type' => $receiveObjectType])
                        ->whereJsonContains('payload->txId', $payload->txId)
                        ->first();

                    $apiLogB = ($cryptoReceiveApiLog) ? $cryptoReceiveApiLog : new CryptoAssetApiLog() ;
                    $apiLogB->object_type = $receiveObjectType;
                    $apiLogB->payment_method_id  = TatumIo;
                    $apiLogB->object_id  = $transactionB->id;
                    $apiLogB->network  = $apiLog->network;
                    $apiLogB->confirmations  = 7;
                    $apiLogB->payload = json_encode($payload2);
                    $apiLogB->save();

                }

                return true;

            }

            return true;
        }
    }

    private function cryptoReceivedAnonymous()
    {
        $tatumWalletApiLog = $this->userCryptoWalletUpdate($this->data?->address);
        $currencyId = optional($tatumWalletApiLog->wallet)->currency_id;

        if ($this->data?->type == 'trc20') {

            $tatumIo = new TatumIoTransaction($tatumWalletApiLog->network);
            $tatumIo->tatumIoAsset();
            $tokenDetails = $this->tatumIo->getTokenTransactionDetails($this->data?->txId);
            $tokenAddress = $tokenDetails->rawData?->contract[0]?->parameter?->value?->contractAddressBase58;

            $this->tokenWalletUpdate($this->data?->txId, $this->data?->address);
            $cryptoToken = CryptoToken::where('address', $tokenAddress)->first();

            if (empty($cryptoToken)) {
                return;
            }

            $currencyId = $cryptoToken->currency_id;
        }
        
        \Log::info( $tatumWalletApiLog);

        if (!empty($tatumWalletApiLog)) {

            $transactionType = ($this->data->type == 'trc20') ? Token_Received : Crypto_Received;

            $arr = [
                'userId' => optional($tatumWalletApiLog->wallet)->user_id,
                'endUserId' => null,
                'currencyId' => $currencyId,
                'uniqueCode' => unique_code(),
                'transactionType' => $transactionType,
                'amount' => $this->data?->amount,
                'status' => 'Success',
            ];

            \Log::info($arr);

            $this->transactionDetails = $this->tatumIo->transactionView($this->data?->txId);

            \Log::info($this->transactionDetails);

            $transactionId = $this->tatumIo->createCryptoTransaction($arr);

            \Log::info($transactionId);

            $arr['transactionId'] = $transactionId;
            $arr['walletCurrencyCode'] = $tatumWalletApiLog->network;
            $arr['object_type'] = ($this->data->type == 'trc20') ? 'token_received' : 'crypto_received';
            $arr['confirmations'] = 7;
            $arr['withdrawInfoData']['txId'] = $this->data?->txId;
            $arr['withdrawInfoData']['receiverAddress'] = $this->data?->address;
            $arr['withdrawInfoData']['senderAddress'] = $this->transactionDetails['senderAddress'] ?? '';

            $cryptoApiLog = $this->tatumIo->createWithdrawalOrSendCryptoApiLog($arr);

            \Log::info( $cryptoApiLog);
        }

        return true;
    }

    private function tokenWalletUpdate($txId, $nativeAddress)
    {
        try {
            $tatumWalletApiLog = tatumGetWalletApiLog($nativeAddress);

            $tatumIo = new TatumIoTransaction($tatumWalletApiLog->network);
            $tatumIo->tatumIoAsset();

            $tokenDetails = $this->tatumIo->getTokenTransactionDetails($txId);

            $tokenAddress = $tokenDetails->rawData?->contract[0]?->parameter?->value?->contractAddressBase58;

            $tatumIo->checkUserTatumWallet(optional($tatumWalletApiLog->wallet)->user_id);
            $tokenBalance = $tatumIo->getTokenBalanceByAddress($nativeAddress, $tokenAddress);

            $cryptoToken = CryptoToken::where('address', $tokenAddress)->first();

            if (empty($cryptoToken)) {
                return;
            }

            $balance = !is_null($tokenBalance) ? getTokenDecimalBalance($tokenBalance, $cryptoToken->decimals) : $this->data->amount;

            Wallet::updateOrCreate(
                [
                    'user_id' => optional($tatumWalletApiLog->wallet)->user_id,
                    'currency_id' => $cryptoToken->currency_id
                ],
                ['balance' => $balance]
            );
    
        } catch (Exception $e) {
            \Log::info('token wallet update fail '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        }
    }

}
