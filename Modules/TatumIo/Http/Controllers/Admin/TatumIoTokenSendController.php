<?php

namespace Modules\TatumIo\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\{
    CryptoProvider, 
    Wallet
};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\TatumIo\Class\TatumIoTransaction;
use Modules\TatumIo\Entities\CryptoToken;

class TatumIoTokenSendController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function tokenSendCreate($network, $token)
    {
        $data['menu'] = 'crypto_token';
        $data['network'] = $network = decrypt($network);
        $data['currency'] = \App\Models\Currency::where(['code' => $network, 'type' => 'crypto_asset'])->first(['id', 'type', 'status']);
        $currencyId = $data['currency']->id;


        if ($data['currency']->status != 'Active') {
            Common::one_time_message('error', __('Please activate the :x first for making any transaction', ['x' => $network]));
            return redirect()->route('admin.tatumio.token');
        }

        if (CryptoProvider::getStatus('TatumIo') != 'Active') {
            Common::one_time_message('error', __('Please activate the provider first for making any transaction'));
            return redirect()->route('admin.tatumio.token');
        }

        setActionSession();

        $data['token'] = $token = decrypt($token);
        $data['tokenDetails'] = $tokenDetails = CryptoToken::find($token);

        $data['users'] = \App\Models\User::whereHas('wallets.cryptoAssetApiLogs', function ($q) use ($currencyId, $network) {
            $q->where('wallets.currency_id', $currencyId);
            $q->where(['crypto_asset_api_logs.payment_method_id' => TatumIo, 'crypto_asset_api_logs.network' => $network]);
        })
            ->whereStatus('Active')
            ->get();

        $data['minTatumIoLimit'] = tokenFormat(tokenMinLimit($tokenDetails->decimals), $tokenDetails->decimals);

        return view('tatumio::admin.token.send.create', $data);

    }


    //Get merchant network address, merchant network balance and user network address
    public function adminTokenBalance(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $network = $request->network;
            $tokenAddress = $request->tokenAddress;
            $tokenDecimals = $request->tokenDecimals;

            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
            $tatumIo->checkUserTatumWallet($user_id);

            $merchantAddress = $tatumIo->getMerchantAddress();
            $tokenBalance = $tatumIo->getTokenBalanceByAddress($merchantAddress, $tokenAddress);

            return response()->json([
                'status' => 200,
                'merchantAddress' =>  $merchantAddress,
                'merchantAddressBalance' => $tatumIo->getMerchantBalance(),
                'userAddress' => $tatumIo->getUserAddress(),
                'tokenAddress' => $tokenAddress,
                'tokenBalace' =>  getTokenDecimalBalance($tokenBalance, $tokenDecimals)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    /* Crypto Sent :: Confirm */
    public function tokenSendConfirm(Request $request)
    {
        actionSessionCheck();
        $data['menu'] = 'crypto_token';
        $data['users'] = \App\Models\User::find($request->user_id, ['id', 'first_name', 'last_name']);

        $response = $this->tokenSendReceiveConfirm($data, $request, 'send');

        if ($response['status'] == 401) {
            Common::one_time_message('error', $response['message']);
            return redirect()->route('admin.tatumio.token.send', ['network' => encrypt($request->network), 'tokenid' => encrypt($request->token_id)]);
        }
        // For confirm page only
        $data['cryptoTrx'] = $response['cryptoTrx'];

        return view('tatumio::admin.token.send.confirmation', $data);
    }

     /**
     * Common functions for Token Sent Receive starts from here
     *
     */

    public function tokenSendReceiveConfirm($data, $request, $type)
    {
        $userId = $request->user_id;
        $network = $request->network;
        $token = $request->token;
        $amount = $request->amount;
        $minNetworkFee = 10;
        $merchantAddress = $request->merchantAddress;
        $userAddress = $request->userAddress;
        $tokenAddress = $request->merchantTokenAddress;
        $currency = (new \App\Models\Currency())->getCurrency(['code' => $token, 'type' => 'crypto_token'], ['id', 'symbol']);
        $cryptoToken = CryptoToken::where(['symbol' => $token], ['network' => $request->network])->first();

        //merge currency symbol with request array
        $request->merge(['currency_symbol' => $currency->symbol]);
        $request->merge(['currency_id' => $currency->id]);
        $request->merge(['user_full_name' => getColumnValue($data['users'])]);
        $request->merge(['token_id' => $cryptoToken->id]);
        $request->merge(['token_decimals' => $cryptoToken->decimals]);


        $minTatumIoLimit = tokenFormat(tokenMinLimit($cryptoToken->decimals), $cryptoToken->decimals);

        if ($amount < $minTatumIoLimit) {

            return [
                'status' => 401,
                'message' => __('The minimum amount must be :x', ['x' => $minTatumIoLimit . ' '. $token]),
            ];
        }

        $tatumIo = new TatumIoTransaction($network);
        $tatumIo->tatumIoAsset();
        $tatumIo->checkUserTatumWallet($userId);

        $nativeAddress = ($type == 'send') ?  $merchantAddress : $userAddress;


        $tokenBalance = $tatumIo->getTokenBalanceByAddress($nativeAddress, $tokenAddress);


        if ($amount > $tokenBalance) {
            return [
                'status' => 401,
                'message' => __('Not have enough token'),
            ];
        }

        $availableBalance = ($type == 'send') ? $tatumIo->getMerchantBalance() : $tatumIo->getUserBalance();

        if ($minNetworkFee > $availableBalance) {
            return [
                'status' => 401,
                'message' => __('Insufficient Balance'),
            ];
        }
        //unset users - not needed in confirm page
        unset($data['users']);
        //Call network fee API of tatum io

        //merge network fee with request array
        $request->merge(['network_fee' => $minNetworkFee]);

        //Put data in session for success page
        session(['cryptoTrx' => $request->all()]);

        //for confirm page only
        $data['cryptoTrx'] = $request->only('currency_symbol', 'currency_id', 'network', 'amount', 'network_fee', 'user_id', 'user_full_name', 'token_id', 'token_decimals');

        return [
            'cryptoTrx' => $data['cryptoTrx'],
            'status' => 200,
        ];

    }

    /* Token Sent :: success */
    public function tokenSendSuccess(Request $request)
    {
       
        actionSessionCheck();

        $res = $this->tokenSendReceiveSuccess($request, 'send');

        if ($res['status'] == 401) {
           Common::one_time_message('error', $res['message']);
           return redirect()->route('admin.tatumio.token.send', ['network' => encrypt($request->network), 'tokenid' => encrypt($request->token_id)]);
        }

        return view('tatumio::admin.token.send.success', $res['data']);
    }

    public function tokenSendReceiveSuccess($request, $type)
    {
        $minNetworkFee = 10;
        $network = $request->network;
        $cryptoTrx = session('cryptoTrx');

        if (empty($cryptoTrx)) {
            return [
                'message' => null,
                'network' => $network,
                'status' => 401,
            ];
        }
        
        // Backend validation of sender crypto wallet balance -- for multiple tab submit
        $request['network'] = $cryptoTrx['network'];
        $request['merchantAddress'] = $cryptoTrx['merchantAddress'];
        $request['userAddress'] = $cryptoTrx['userAddress'];
        $request['amount'] = $cryptoTrx['amount'];
        $request['tokenAddress'] = $cryptoTrx['merchantTokenAddress'];

        $tatumIo = new TatumIoTransaction($network);
        $tatumIo->tatumIoAsset();
        $tatumIo->checkUserTatumWallet($cryptoTrx['user_id']);


        $availableBalance = ($type == 'send') ? $tatumIo->getMerchantBalance() : $tatumIo->getUserBalance();

        if ($minNetworkFee > $availableBalance) {
            return [
                'status' => 401,
                'message' => __('Insufficient Balance'),
            ];
        }

        $nativeAddress = ($type == 'send') ?  $cryptoTrx['merchantAddress'] : $cryptoTrx['userAddress'];

        $tokenBalance = $tatumIo->getTokenBalanceByAddress($nativeAddress, $cryptoTrx['merchantTokenAddress']);

        if ($cryptoTrx['amount'] > $tokenBalance) {
            return [
                'status' => 401,
                'message' => __('Not have enough token'),
            ];
        }


        try {
            $uniqueCode = unique_code();
            $arr = [
                'walletCurrencyCode' => $cryptoTrx['token'],
                'amount' => $cryptoTrx['amount'],
                'networkFee' => $cryptoTrx['network_fee'],
                'userId' => null,
                'endUserId' => null,
                'currencyId' => $cryptoTrx['currency_id'],
                'currencySymbol' => $cryptoTrx['currency_symbol'],
                'uniqueCode' => $uniqueCode,
                'confirmation' => 0,
                'status' => 'Pending'
            ];

            if ($type === 'send') {
                $arr['senderAddress'] = $cryptoTrx['merchantAddress'];
                $arr['receiverAddress'] = $cryptoTrx['userAddress'];
                $arr['endUserId'] = $cryptoTrx['user_id'];
                $arr['transactionType'] = Token_Sent;
                $arr['object_type'] = 'token_sent';
            } elseif ($type == 'receive') {
                $arr['senderAddress'] = $cryptoTrx['userAddress'];
                $arr['receiverAddress'] = $cryptoTrx['merchantAddress'];
                $arr['endUserId'] = $cryptoTrx['user_id'];
                $arr['transactionType'] = Token_Received;
                $arr['object_type'] = 'token_received';


            }

            if ($type == 'send') {
                $withdrawInfoResponse = $tatumIo->sendTokenAdminToUser($cryptoTrx['merchantTokenAddress'], $cryptoTrx['amount']);
            } else {
                $withdrawInfoResponse = $tatumIo->sendTokenUserToAdmin($cryptoTrx['merchantTokenAddress'], $cryptoTrx['amount']);
            }

            if (!isset($withdrawInfoResponse->txId)) {
                return [
                    'message' => isset($withdrawInfoResponse->cause) ?  $withdrawInfoResponse->cause : __('Transaction Failed, please try again'),
                    'network' => $network,
                    'status' => 401,
                ];
            }

            DB::beginTransaction();

            $withdrawInfoResponse = $tatumIo->getTokenTransactionDetails($withdrawInfoResponse->txId);

            Log::info(json_encode($withdrawInfoResponse));

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $cryptoTrx['user_id'], 'currency_id' => $cryptoTrx['currency_id']]
            );

            if ($type == 'send') {
                $wallet->increment('balance', $cryptoTrx['amount']);
            } else {
                $wallet->decrement('balance', $cryptoTrx['amount']);
            }

            $cryptoToken = CryptoToken::find($cryptoTrx['token_id']);

            if ($cryptoToken && $type == 'send') {
                $cryptoToken->decrement('value', $cryptoTrx['amount']);
            } else {
                $cryptoToken->increment('value', $cryptoTrx['amount']);
            }
            

            // Create Merchant Crypto Transaction
            $createCryptoTransactionId = $tatumIo->createTokenTransaction($arr);

            // Create merchant new withdrawal/Send/Receive crypt api log
            $arr['transactionId'] = $createCryptoTransactionId;

            $withdrawInfoData = [
                'txId' => isset($withdrawInfoResponse->txID) ? $withdrawInfoResponse->txID : null ,
                'senderAddress' => ($type === 'send') ? $cryptoTrx['merchantAddress'] : $cryptoTrx['userAddress'],
                'receiverAddress' => ($type === 'send') ? $cryptoTrx['userAddress'] : $cryptoTrx['merchantAddress'] ,
                'network_fee' => isset($withdrawInfoResponse->fee)? getTokenDecimalBalance($withdrawInfoResponse->fee, $cryptoTrx['token_decimals']) : 0,
                'token' => $cryptoTrx['token'],
                'network' => $cryptoTrx['network']
            ];

            $arr['withdrawInfoData'] = $withdrawInfoData;

          
            $tatumIo->createWithdrawalOrSendTokenApiLog($arr);

            DB::commit();

            $data['confirmations'] = 0;
            $data['walletCurrencyCode'] = $arr['walletCurrencyCode'];
            $data['receiverAddress'] = $arr['receiverAddress'];
            $data['currencySymbol'] = $arr['currencySymbol'];
            $data['currencyId'] = $arr['currencyId'];
            $data['amount'] = $arr['amount'];
            $data['transactionId'] = $arr['transactionId'];
            $data['tokenId'] = $cryptoTrx['token_id'];
            $data['network'] = $cryptoTrx['network'];
            $data['menu'] = 'crypto_token';
            $data['token_decimals'] = $cryptoTrx['token_decimals'];

            if ($type === 'send') {
                $data['userId'] = $arr['endUserId'];
            } elseif ($type === 'receive') {
                $data['userId'] = $arr['userId'];
            }
            $data['user_full_name'] = $cryptoTrx['user_full_name'];

            //clear cryptoTrx from session
            session()->forget(['cryptoTrx']);
            clearActionSession();
            return [
                'data' => $data,
                'status' => 200,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            session()->forget(['cryptoTrx']);
            clearActionSession();
            return [
                'message' => $e->getMessage(),
                'network' => $network,
                'status' => 401,
            ];
        }
        
    }

    //validate merchant Address Balance Against Amount
    public function validateAdminBalanceToSendToken(Request $request)
    {
        $message = '';
        $status = 200;
        $minAvailableAmount = 50;
        $amount = $request->amount;
        $network = $request->network;
        $tokenAddress = $request->tokenAddress;
        $tokenDecimals = $request->tokenDecimals;
        $merchantAddress = $request->merchantAddress;

        try {

            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
            $merchantBalance = $tatumIo->getMerchantBalance();
            $tokenBalance = getTokenDecimalBalance(
                $tatumIo->getTokenBalanceByAddress($merchantAddress, $tokenAddress), 
                $tokenDecimals
            );

            if ($tokenBalance < $amount) {
                $status = 401;
                $message = __('Not have enough token');
            }

            if ( $status == 200 &&  $merchantBalance < $minAvailableAmount) {
                $status = 401;
                $message =  __('minimum :x  needs for network fee', ['x' => $minAvailableAmount. ' ' . $network]);   
            }

            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);

       
        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /* Token Receive :: Create */
    public function tokenReceiveInitiate($network, $token)
    {
        $data['menu'] = 'crypto_token';
        $data['network'] = $network = decrypt($network);
        $data['currency'] = \App\Models\Currency::where(['code' => $network, 'type' => 'crypto_asset'])->first(['id', 'type', 'status']);
        $currencyId = $data['currency']->id;

        if ($data['currency']->status != 'Active') {
            Common::one_time_message('error', __('Please activate the :x first for making any transaction', ['x' => $network]));
            return redirect()->route('admin.crypto_providers.list', 'TatumIo');
        }

        if (CryptoProvider::getStatus('TatumIo') != 'Active') {
            Common::one_time_message('error', __('Please activate the provider first for making any transaction'));
            return redirect()->route('admin.crypto_providers.list', 'TatumIo');
        }

        $data['token'] = $token = decrypt($token);
        $data['tokenDetails'] = $tokenDetails = CryptoToken::find($token);


        setActionSession();
        $data['users'] = \App\Models\User::whereHas('wallets.cryptoAssetApiLogs', function ($q) use ($currencyId, $network) {
            $q->where('wallets.currency_id', $currencyId);
            $q->where(['crypto_asset_api_logs.payment_method_id' => TatumIo, 'crypto_asset_api_logs.network' => $network]);
        })
            ->get();

        $data['minTatumIoLimit'] = tokenFormat(tokenMinLimit($tokenDetails->decimals), $tokenDetails->decimals);

        return view('tatumio::admin.token.receive.create', $data);
    }

    //Get merchant network address, merchant network balance and user network address
    public function userTokenBalance(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $network = $request->network;
            $tokenDecimals = $request->tokenDecimals;

            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
            $tatumIo->checkUserTatumWallet($user_id);
            $userAddress = $tatumIo->getUserAddress();
            $tokenBalance = $tatumIo->getTokenBalanceByAddress($userAddress, $request->tokenAddress);

            return response()->json([
                'status' => 200,
                'userAddress' => $tatumIo->getUserAddress(),
                'userAddressBalance' => $tatumIo->getUserBalance(),
                'merchantAddress' => $tatumIo->getMerchantAddress(),
                'tokenAddress' => $request->tokenAddress,
                'tokenBalance' => getTokenDecimalBalance($tokenBalance, $tokenDecimals)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage(),
            ]);
        }
    }

    //validate merchant Address Balance Against Amount
    public function validateUserBalanceToSendToken(Request $request)
    {
        $message = '';
        $status = 200;
        $minAvailableAmount = 10;
        $amount = $request->amount;
        $network = $request->network;
        $tokenAddress = $request->tokenAddress;
        $tokenDecimals = $request->tokenDecimals;
        $userAddress = $request->userAddress;

        try {

            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
            $tatumIo->checkUserTatumWallet($request->userId);

            $userBalance = $tatumIo->getUserBalance();
            $tokenBalance = getTokenDecimalBalance(
                $tatumIo->getTokenBalanceByAddress($userAddress, $tokenAddress), 
                $tokenDecimals
            );

            if ($tokenBalance < $amount) {
                $status = 401;
                $message = __('Not have enough token');
            }

            if ( $status == 200 &&  $userBalance < $minAvailableAmount) {
                $status = 401;
                $message =  __('minimum :x  needs for network fee', ['x' => $minAvailableAmount. ' ' . $network]);   
            }

            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);

       
        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage().$e->getLine().$e->getFile(),
            ]);
        }
    }

    /* Token Receive :: Confirm */
    public function tokenReceiveConfirm(Request $request)
    {
        actionSessionCheck();
        $data['menu'] = 'crypto_token';
        $data['users'] = \App\Models\User::find($request->user_id, ['id', 'first_name', 'last_name']);
        

        $response = $this->tokenSendReceiveConfirm($data, $request, 'receive');


        if ($response['status'] == 401) {
            Common::one_time_message('error', $response['message']);
            return redirect()->route('admin.tatumio.token.receive', ['network' => encrypt($request->network), 'tokenid' => encrypt($request->token_id)]);
        }
        // For confirm page only
        $data['cryptoTrx'] = $response['cryptoTrx'];

        return view('tatumio::admin.token.receive.confirmation', $data);
    }

    /* Token Sent :: success */
    public function tokenReceiveSuccess(Request $request)
    {
        actionSessionCheck();

        $res = $this->tokenSendReceiveSuccess($request, 'receive');

        if ($res['status'] == 401) {
           Common::one_time_message('error', $res['message']);
           return redirect()->route('admin.tatumio.token.send', ['network' => encrypt($request->network), 'tokenid' => encrypt($request->token_id)]);
        }
        $res['menu'] = 'crypto_token';

        return view('tatumio::admin.token.receive.success', $res['data']);
    }

    public function printPdf($id)
    {
        $id = decrypt($id);
        $data['transaction'] = $transaction = \App\Models\Transaction::with(['currency:id,symbol,code', 'cryptoAssetApiLog:id,object_id,payload,confirmations'])->where(['id' => $id])->first();

        if (!empty($transaction->cryptoAssetApiLog)) {
            $getCryptoDetails = getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoAssetApiLog?->payload, $transaction->cryptoAssetApiLog?->confirmations);
            if (count($getCryptoDetails) > 0) {
                if (isset($getCryptoDetails['senderAddress'])) {
                    $data['senderAddress'] = $getCryptoDetails['senderAddress'];
                }
                $data['receiverAddress'] = $getCryptoDetails['receiverAddress'];
                $data['confirmations'] = $getCryptoDetails['confirmations'];
                $data['network_fee'] = isset($getCryptoDetails['network_fee']) ? $getCryptoDetails['network_fee'] : 0.00000000;
                $data['network'] = isset($getCryptoDetails['network']) ? $getCryptoDetails['network'] : null;
                $data['txId'] = isset($getCryptoDetails['txId']) ? $getCryptoDetails['txId'] : null;
            }
        }

        generatePDF('tatumio::user.transactions.token_transaction_pdf', 'crypto-transaction_', $data);
    }
}
