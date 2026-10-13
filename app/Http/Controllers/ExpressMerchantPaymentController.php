<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpressMerchantPaymentException;
use App\Services\ExpressMerchantPaymentService;
use Session, Auth, Exception;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use App\Models\{
    AppTransactionsInfo,
    Currency,
    CurrencyPaymentMethod,
    MerchantPayment,
    PaymentMethod
};

class ExpressMerchantPaymentController extends Controller
{
    protected $helper;
    protected $service;
    public function __construct()
    {
        $this->helper = new Common();
        $this->service = new ExpressMerchantPaymentService();
    }

    public function verifyClient(Request $request)
    {
        try {
            $app = $this->service->verifyClientCredentials($request->client_id, $request->client_secret);
            $response = $this->service->createAccessToken($app);
            return json_encode($response);

        } catch (ExpressMerchantPaymentException $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return json_encode($data);

        } catch(Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => __("Failed to process the request."),
            ];
            return json_encode($data);
        }
        
    }

    public function storeTransactionInfo(Request $request)
    {
        try {
            $paymentMethod = $request->payer;
            $amount        = $request->amount;
            $currency      = $request->currency;
            $successUrl    = $request->successUrl;
            $cancelUrl     = $request->cancelUrl;

            # check token missing
            $hasHeaderAuthorization = $request->hasHeader('Authorization');
            if (!$hasHeaderAuthorization) {
                $res = [
                    'status'  => 'error',
                    'message' => __('Access token is missing'),
                    'data'    => [],
                ];
                return json_encode($res);
            }

            # check token authorization
            $headerAuthorization = $request->header('Authorization');
            $token = $this->service->checkTokenAuthorization($headerAuthorization);

            # Currency And Amount Validation
            $res = $this->service->checkMerchantWalletAvailability($token, $currency, $amount);

            # Update/Create AppTransactionsInfo and return response
            $res = $this->service->createAppTransactionsInfo($token->app_id, $paymentMethod, $amount, $currency, $successUrl, $cancelUrl);
            return json_encode($res);
        } catch (ExpressMerchantPaymentException $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return json_encode($data);

        } catch(Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => __("Failed to process the request."),
            ];
            return json_encode($data);
        }
    }

    /**
     * [Generat URL]
     * @param  Request $request  [email, password]
     * @return [view]  [redirect to merchant confirm page or redirect back]
     */
    public function generatedUrl(Request $request)
    {
        try {

            $transInfo = $this->service->getTransactionData($request->grant_id, $request->token);

            $currency = Currency::whereCode($transInfo->currency)->first();
            $feesLimit = $this->service->checkMerchantPaymentFeesLimit($currency->id, Mts, $transInfo->amount, $transInfo->app->merchant->fee);
            $data = $this->service->checkoutToPaymentConfirmPage($transInfo);

            $data['fees'] = $feesLimit['totalFee'];
            $data['currencyId'] = $currency->id;
            $data['totalAmount'] = $data['fee_bearer'] == 'Merchant' ? $transInfo['amount'] : $transInfo['amount'] + $feesLimit['totalFee'];

            setPaymentData($data);

            $data['payment_methods'] = PaymentMethod::whereStatus('Active')->get(['id', 'name'])->toArray();

            $cpmWithoutMts = CurrencyPaymentMethod::where(['currency_id' => $currency->id])
            ->where('activated_for', 'like', "%deposit%")->pluck('method_id')->toArray();

            $paymoney = PaymentMethod::whereName('Mts')->first(['id']);
            array_push($cpmWithoutMts, $paymoney->id);
            $data['cpm'] = $cpmWithoutMts;

            return view('merchantPayment.confirm', $data);

        } catch (ExpressMerchantPaymentException $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return view('merchantPayment.fail', $data);

        } catch(Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => __("Failed to process the request."),
            ];
            return view('merchantPayment.fail', $data);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {

            $data = getPaymentData();

            $this->checkMerchantUser($data['transInfo']);

            $this->checkUserStatus(auth()->user()->status);

            $this->service->checkUserBalance(auth()->user()->id, $data['totalAmount'], $data['currencyId']);

            $paymentMethod = PaymentMethod::whereName($request->method)->first(['id', 'name']);
            $methodId = $paymentMethod['id'];

            $paymentData = [
                'currency_id' =>  $data['currencyId'],
                'currencySymbol' => $data['currSymbol'],
                'currencyCode' => $data['currCode'],
                'currencyType' => 'fiat',
                'amount' => $data['totalAmount'],
                'total' => $data['totalAmount'],
                'totalAmount' => $data['totalAmount'],
                'transaction_type' => Payment_Sent,
                'payment_type' => 'deposit',
                'payment_method' => $methodId,
                'payment_method_id' => $methodId,
                'redirectUrl' => route('express.payment.succss'),
                'success_url' => route('express.payment.redirect'),
                'cancel_url' => url('payment/fail'),
                'gateway' => strtolower($request->method),
                'payer_id' => auth()->id(),
                'uuid' => unique_code()
            ];
    
            $paymentData = array_merge($data, $paymentData);

            setPaymentData($paymentData);
    
            return redirect(gatewayPaymentUrl($paymentData));

        } catch (ExpressMerchantPaymentException $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return view('merchantPayment.fail', $data);

        } catch(Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => __("Failed to process the request."),
            ];
            return view('merchantPayment.fail', $data);
        }
        
    }


    public function paymentSuccess()
    {
        try {
            $data = $this->service->storePaymentInformations();
            
            if ($data['status'] == 200) {
                if (isset(request()->execute) && (request()->execute == 'api')) {
                    return  $data['transaction_id'];
                }
                getPaymentData('forget');
                return redirect()->to($data['successPath']);
            } 

        } catch (ExpressMerchantPaymentException $exception ) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return view('merchantPayment.fail', $data);

        }  catch(Exception $exception) {
            $data = [
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ];
            return view('merchantPayment.fail', $data);
        }
        
    }

    public function redirectSuccessPath()
    {
        try {
            $data = getPaymentData('forget');
            $transInfo = $data['transInfo'];
            $merchantPayment = MerchantPayment::where('uuid', $data['uuid'])->first();
            $url = $this->service->generateSuccessUrl($merchantPayment, $transInfo['success_url']);
             return redirect()->to($url);
        } catch (Exception $e) {
            $data = [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
            return view('merchantPayment.fail', $data);
        }
        
    }

    public function cancelPayment()
    {
        $transInfo     = Session::get('transInfo');
        $trans         = AppTransactionsInfo::find($transInfo->id, ['id', 'status', 'cancel_url']);
        $trans->status = 'cancel';
        $trans->save();
        Session::forget('transInfo');
        return redirect()->to($trans->cancel_url);
    }

    protected function checkUserStatus($status)
    {
        //Check whether user is Suspended
        if ($status == 'Suspended') {
            $data['message'] = __('You are suspended to do any kind of transaction!');
            return view('merchantPayment.user_suspended', $data);
        }

        //Check whether user is inactive
        if ($status == 'Inactive') {
            auth()->logout();
            $this->helper->one_time_message('danger', __('Your account is inactivated. Please try again later!'));
            return redirect('/login');
        }
    }

    protected function checkMerchantUser(object $transInfo)
    {
        if ($transInfo?->app?->merchant?->user?->id == auth()->user()->id) {
            auth()->logout();
            $this->helper->one_time_message('error', __('Merchant cannot make payment to himself!'));
            return redirect()->back();
        } 
    }
}
