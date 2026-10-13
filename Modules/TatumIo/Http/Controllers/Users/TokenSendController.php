<?php

namespace Modules\TatumIo\Http\Controllers\Users;

use App\Http\Helpers\Common;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TatumIo\Http\Requests\TokenSendRequest;
use Modules\TatumIo\Services\CryptoSendService;

class TokenSendController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new CryptoSendService();
    }

    public function sendTokenCreate($walletCurrencyCode, $walletId)
    {
        // destroying cryptoEncArr after loading create poge from reload of crypto success page
        if (!empty(session('cryptoEncArr'))) {
            session()->forget('cryptoEncArr');
        }
        //set the session for validating the action
        setActionSession();

        try {
            $cryptoToken = $this->service->getUserTokenNetwork(
                decrypt($walletId), 
                decrypt($walletCurrencyCode)
            );

            $network =  $cryptoToken->network; 

            $address = $this->service->userAddress($network);

            $data = [
                'currencyType' => 'crypto_token',
                'senderAddress' => encrypt($address['senderAddress']),
                'walletCurrencyCode' => decrypt($walletCurrencyCode),
                'walletId' => decrypt($walletId),
                'network' => $network,
                'cryptoToken' => $cryptoToken,
                'minTatumIoLimit' => tokenFormat(tokenMinLimit($cryptoToken->decimals), $cryptoToken->decimals)
            ];

            return view('tatumio::user.token.send.create', $data);

        } catch (Exception $th) {
            $data['message'] = __($th->getMessage());
            return redirect('wallet-list');
        }

    }

    public function sendTokenConfirm(TokenSendRequest $request)
    {
        actionSessionCheck();

        $walletCurrencyCode = decrypt($request->walletCurrencyCode);
        $walletId = decrypt($request->walletId);
        $amount = $request->amount;
        $receiverAddress = $request->receiverAddress;
        try {

            $arr = $this->service->userTokenBalanceCheck(
                $walletId, 
                $walletCurrencyCode, 
                $receiverAddress, 
                $amount
            );

            $arr['walletId'] =  $walletId;

            setPaymentData($arr);

            //Put currency code and wallet into session id for create route & destroy it after loading create poge - starts
            $cryptoEncArr = [];
            $cryptoEncArr['walletCurrencyCode'] = $walletCurrencyCode;
            $cryptoEncArr['walletId'] = $walletId;
            session(['cryptoEncArr' => $cryptoEncArr]);
            // Data for confirm page - starts
            $data['cryptoTrx'] = $arr;
            $data['walletCurrencyCode'] = $walletCurrencyCode;
            $data['walletId'] = $walletId;
            $data['currencyId'] = $arr['currencyId'];

            return view('tatumio::user.token.send.confirmation', $data);

        } catch (Exception $e) {
            return back()->withErrors(__($e->getMessage()))->withInput();
        }

    }

    public function sendTokenSuccess(Request $request)
    {
        $cryptoTrx = getPaymentData();
        
        if (empty($cryptoTrx)) {
            return redirect()->route('user.token_send.create', [encrypt(session('cryptoEncArr')['walletCurrencyCode']), encrypt(session('cryptoEncArr')['walletId'])]);
        }

        actionSessionCheck();

        try {

            $response  = $this->service->sendTokenFinal(
                $cryptoTrx['network'], 
                $cryptoTrx['walletCurrencyCode'],
                $cryptoTrx['walletId'], 
                $cryptoTrx['receiverAddress'], 
                $cryptoTrx['amount'], 
            );

            $data['walletCurrencyCode'] = $cryptoTrx['walletCurrencyCode'];
            $data['receiverAddress'] = $cryptoTrx['receiverAddress'];
            $data['currencySymbol'] = $cryptoTrx['currencySymbol'];
            $data['currencyId'] = $cryptoTrx['currencyId'];
            $data['amount'] = $cryptoTrx['amount'];
            $data['transactionId'] = $response['transactionId'];
            $data['walletId'] = session('cryptoEncArr')['walletId'];

            // Don't flush/forget cryptoEncArr from session as it will be cleared on create method
            session()->forget(['cryptoTrx']);
            clearActionSession();

            return view('tatumio::user.token.send.success', $data);


        } catch (Exception $e) {

            Common::one_time_message('error', $e->getMessage());
            return redirect()->route('tatumio.user.token_send.create', [encrypt(session('cryptoEncArr')['walletCurrencyCode']), encrypt(session('cryptoEncArr')['walletId'])]);
        }

    }

    // Validate crypto address
    public function validateCryptoAddress(Request $request)
    {
        try {
            $network = $request->network;
            $address = $request->receiverAddress;
            return $this->service->cryptoAddressValidation($network, $address, 'web');
        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage(),
            ]);
        }
    }

    //validate merchant Address Balance Against Amount
    public function validateUserBalanceAgainstAmount(Request $request)
    {
        try {
            extract($request->only(['walletId', 'walletCurrencyCode', 'receiverAddress', 'amount']));

            return $this->service->userTokenBalanceCheck(
                decrypt($walletId), 
                $walletCurrencyCode, 
                $receiverAddress, 
                $amount
            );

        } catch (Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function printPdf($id)
    {
        $data['transaction'] = $transaction = Transaction::with(['currency:id,symbol,code', 'cryptoAssetApiLog:id,object_id,payload,confirmations'])->where(['id' => $id])->first();

        if (!empty($transaction->cryptoAssetApiLog)) {
            $getCryptoDetails = getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, optional($transaction->cryptoAssetApiLog)->payload, optional($transaction->cryptoAssetApiLog)->confirmations);
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
