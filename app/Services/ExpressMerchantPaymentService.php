<?php

/**
 * @package ExpressMerchantPaymentService
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 25-06-2023
 */

namespace App\Services;

use App\Exceptions\ExpressMerchantPaymentException;
use App\Http\Helpers\Common;
use App\Models\{AppToken, AppTransactionsInfo, Currency, MerchantApp,
    FeesLimit,
    Merchant,
    MerchantPayment,
    Transaction,
    Wallet
};
use App\Services\Mail\MerchantPayment\NotifyAdminOnPaymentMailService;
use App\Services\Mail\MerchantPayment\NotifyMerchantOnPaymentMailService;
use Str, Exception, Session, DB;

class ExpressMerchantPaymentService
{
    protected $helper;
    protected $feeBearer;
    protected $fee;
    protected $merchantId;
    protected $userId;
    protected $uniqueCode;
    protected $amount;
    protected $currencyCode;
    protected $currencyId;
    protected $paymentMethodId;
    protected $payerId;

    public function __construct()
    {
        $this->helper = new Common();
    }

    /** 
     * Verify merchant validity through clientId and clientSecret
     * 
     * @param string $clientId
     * @param string $clientSecret
     * 
     * @return object
     * 
     * @through MerchantWithdrawException
    **/

    public function verifyClientCredentials(string $clientId, string $clientSecret): object
    {
        $merchantApp = MerchantApp::with('merchant:id,user_id')->where([
            'client_id' => $clientId, 
            'client_secret' => $clientSecret
        ])->first();
 
        self::checkNullAndThrowException($merchantApp, __('Can not verify the client. Please check client Id and Client Secret.'), 'merchantNotFound');
 
        $merchant = Merchant::where('id', $merchantApp->merchant_id)->first('status');
 
        self::checkNullAndThrowException($merchant, __('Can not verify the client. Please check client Id and Client Secret.'), 'merchantNotFound');
 
        if ($merchant->status == 'Moderation' || $merchant->status == 'Disapproved') {
            self::throwException(__('Merchant is temporarily unavailable.'), 'merchantNotFound');
        }
 
        return $merchantApp;
    }

     /**
     * Method createAccessToken
     *
     * @param object $app
     *
     * @return array
     */
    public function createAccessToken(object $app): array
    {
        $appToken = $app->accessToken()->create([
            'token' => Str::random(26), 
            'expires_in' => time() + 3600
        ]);

        return [
            'status'  => 'success',
            'message' => 'Client Verified',
            'data'    => [
                'access_token' => $appToken->token,
            ],
        ];
    }

    /**
     * Method checkTokenAuthorization
     *
     * @param string $headerAuthorization
     *
     * @return object
     * 
     * @through MerchantWithdrawException
     */
    public function checkTokenAuthorization(string $headerAuthorization): object
    {
        $accessToken = $headerAuthorization;
        $actualToken = '';

        if (preg_match('/\bBearer\b/', $accessToken)) {
            $t           = explode(' ', $accessToken);
            $key         = array_keys($t);
            $last        = end($key);
            $actualToken = $t[$last];
        }

        $appToken = AppToken::where('token', $actualToken)->where('expires_in', '>=', time())->first();
        self::checkNullAndThrowException($appToken, __('Empty token or token has been expired.'), 'sessionExpired');
        return $appToken;
    }

    /**
     * Check merchant wallet availability by appToken and withdrawalCurrencyCode 
     *
     * @param object $appToken
     * @param string $withdrawalCurrencyCode
     *
     * @return void
     * 
     * @through MerchantWithdrawException
     */
    public function checkMerchantWalletAvailability(object $appToken, string $withdrawalCurrencyCode, float $amount): void
    {
        $currencyId = Currency::where('code', $withdrawalCurrencyCode)->value('id');

        $merchantWallet = Wallet::where([
            'user_id' => $appToken?->app?->merchant?->user_id,
            'currency_id' => $currencyId
        ])->first(['id', 'balance', 'user_id']);

        self::checkNullAndThrowException($merchantWallet, __('Currency :x is not supported by this merchant.', ['x' => $withdrawalCurrencyCode]), 'currencyNotFound');

        if ($amount <= 0) {
            self::throwException(__('Amount cannot be 0 or less than 0.'), 'amountZero');
        }
    }

    /**
     * Create app transactions info with tokenAppId, paymentMethod, amount and currency
     *
     * @param int $tokenAppId
     * @param string $paymentMethod
     * @param float $amount
     * @param string $currency
     *
     * @return array
     */
    public function createAppTransactionsInfo(int $tokenAppId, string $paymentMethod, float $amount, string $currency, string $successUrl, string $cancelUrl): array
    {
        $grantId  = random_int(10000000, 99999999);
        $urlToken = Str::random(20);

        $transactionCreate = AppTransactionsInfo::create([
            'app_id'         => $tokenAppId,
            'payment_method' => $paymentMethod,
            'amount'         => $amount,
            'currency'       => $currency,
            'success_url'    => $successUrl,
            'cancel_url'     => $cancelUrl,
            'grant_id'       => $grantId,
            'token'          => $urlToken,
            'status'         => 'pending',
            'expires_in'     => time() + (60 * 60 * 5) //expire in 5 hours after generation
        ]);

        if (!$transactionCreate) {
            self::throwException(__('Failed to create transaction info.'), 'transactionInfoFailed');
        }

        $url = url("merchant/payment?grant_id=$grantId&token=$urlToken");

        return [
            'status' => 'success',
            'message' => __('Transaction Info Created Successfully!'),
            'data'    => [
                'approvedUrl' => $url,
            ],
        ];
    }


    /**
     * Get app transaction info data through grantId and token
     *
     * @param string $grantId 
     * @param string $token
     *
     * @return object
     * 
     * @through MerchantWithdrawException
     */
    public function getTransactionData(string $grantId, string $token): object
    {
        $transactionInfo = AppTransactionsInfo::with([
            'app:id,merchant_id',
            'app.merchant:id,user_id,merchant_group_id,business_name,fee',
            'app.merchant.merchant_group:id,fee_bearer',
            'app.merchant.user:id,first_name,last_name,status',
        ])
        ->where([
            'grant_id' => $grantId, 
            'token' => $token, 
            'status' => 'pending'
        ])
        ->where('expires_in', '>=', time())
        ->first(['id', 'app_id', 'payment_method', 'currency', 'amount', 'success_url']);

        self::checkNullAndThrowException($transactionInfo, __('Session expired.'), 'sessionExpired');
        return $transactionInfo;
    }

    /**
     * check for going to payment confirm page through transaction info
     *
     * @param object $transInfo
     *
     * @return array
     * 
     * @through MerchantWithdrawException
     */
    public function checkoutToPaymentConfirmPage(object $transInfo): array
    {
        self::checkNullAndThrowException($transInfo, __('Url has been deleted or expired.'), 'sessionExpired');

        self::checkNullAndThrowException($transInfo?->app?->merchant?->user, __('Merchant user is temporarily unavailable.'), 'merchantUserNotFound');

        //Check whether merchant is suspended or Inactive
        if ($transInfo?->app?->merchant?->user?->status == 'Suspended' || $transInfo?->app?->merchant?->user?->status == 'Inactive') {
            self::throwException(__('Merchant is temporarily unavailable.'), 'merchantNotFound');
        }
        
        //check if currency exists in wallets
        $availableCurrency = [];

        $wallets = Wallet::with('currency:id,code')->where('user_id', $transInfo?->app?->merchant?->user?->id)->get(['currency_id']); 

        foreach ($wallets as $wallet) {
            $availableCurrency[] = getColumnValue($wallet->currency, 'code');
        }

        if (!in_array($transInfo->currency, $availableCurrency)) {
            self::throwException(__('The :x wallet does not exist for the payment', ['x' => $transInfo->currency]), 'walletNotFound');
        }

        //Put transaction information's to Session
        session()->put('transInfo', $transInfo);

        return [
            'status' => 'Active',
            'transInfo' => $transInfo,
            'fee_bearer' => $transInfo?->app?->merchant?->merchant_group?->fee_bearer,
            'merchant_fee' =>  $transInfo?->app?->merchant?->fee,
            'merchant_id' => $transInfo?->app?->merchant_id,
            'user_id' => $transInfo->app?->merchant?->user_id,
            'currSymbol' => Currency::where('code', $transInfo->currency)->value('symbol'),
            'currCode' => $transInfo->currency,
        ];
    }

    private function setData()
    {
        $data = getPaymentParam(request()->params);
        $this->feeBearer = $data['fee_bearer'];
        $this->fee = $data['merchant_fee'];
        $this->merchantId = $data['merchant_id'];
        $this->userId = $data['user_id'];
        $this->payerId = $data['payer_id'];
        $this->uniqueCode = $data['uuid'];
        $this->amount = $data['transInfo']['amount'];
        $this->currencyCode = $data['transInfo']['currency'];
        $this->paymentMethodId = $data['payment_method'];
        $this->currencyId = $data['currencyId'];

        return $data;
    }

    /**
     * confirm transaction payment by user, grantId and token
     *
     * @return array
     * 
     * @through MerchantWithdrawException
     */
    public function storePaymentInformations()
    {  
        try {
            DB::beginTransaction();

            $data = $this->setData();

            $transInfo = $data['transInfo'];

            $feesLimit = self::checkMerchantPaymentFeesLimit(
                $this->currencyId, 
                $this->paymentMethodId, 
                $this->amount, 
                $this->fee
            );

            self::checkMerchant();

            if ($this->paymentMethodId == Mts) {
                self::senderWalletOperation($feesLimit);
            }

            $merchantPayment = $this->merchantPayment($feesLimit);

            $userTransaction = self::createTransaction(
                $merchantPayment->id, 
                $feesLimit, 
                Payment_Sent
            );

            self::createTransaction(
                $merchantPayment->id, 
                $feesLimit, 
                Payment_Received
            );

            self::merchantWalletUpdate($feesLimit);

            DB::commit();

            self::sendNotification($merchantPayment, $feesLimit);

            return [
                'status' => 200,
                'transaction_id' => $userTransaction,
                'successPath' =>self::generateSuccessUrl($merchantPayment, $transInfo['success_url'])
            ];

        } catch (Exception $e) {
            DB::rollBack();

            return [
                'status' => 401,
                'message' => $e->getMessage()
            ];
        }
    }

    public function checkMerchantPaymentFeesLimit($currencyId, $paymentMethodId, $amount, $merchantFee)
    {
        $feeInfo = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $paymentMethodId])->first(['charge_percentage', 'charge_fixed', 'has_transaction']);

        if (!empty($feeInfo) && $feeInfo->has_transaction == "Yes") {
            $feeInfoChargePercentage          = $feeInfo->charge_percentage;
            $feeInfoChargeFixed               = $feeInfo->charge_fixed;
            $depositCalcPercentVal            = $amount * ($feeInfoChargePercentage / 100);
            $depositTotalFee                  = $depositCalcPercentVal + $feeInfoChargeFixed;
            $merchantCalcPercentValOrTotalFee = $amount * ($merchantFee / 100);
            $totalFee                         = $depositTotalFee + $merchantCalcPercentValOrTotalFee;
        } else {
            $feeInfoChargePercentage          = 0;
            $feeInfoChargeFixed               = 0;
            $depositCalcPercentVal            = 0;
            $depositTotalFee                  = 0;
            $merchantCalcPercentValOrTotalFee = $amount * ($merchantFee / 100);
            $totalFee                         = $depositTotalFee + $merchantCalcPercentValOrTotalFee;
        }

        return [
            'merchantPercentOrTotalFee' => $merchantCalcPercentValOrTotalFee,
            'chargePercentage' => $feeInfoChargePercentage,
            'depositPercent' => $depositCalcPercentVal,
            'depositTotalFee' => $depositTotalFee,
            'chargeFixed' => $feeInfoChargeFixed,
            'totalFee' => $totalFee,
        ];
    }

    public function checkUserBalance(int $userId, float $amount, int $currencyId,)
    {
        $userWallet = Wallet::where(['user_id' => $userId, 'currency_id' => $currencyId])->first(['balance']);

        self::checkNullAndThrowException($userWallet, __('User wallet not found.'), 'walletNotFound');

        if ($userWallet->balance < $amount) {
            Self::throwException( __('User does not have sufficient balance.'), 'insufficientBalance');
        }
    }

    public function checkMerchant()
    {
        $merchantUser = Merchant::where('id', $this->merchantId)->first(['id', 'user_id']);

        self::checkNullAndThrowException($merchantUser, __('Merchant not found.'), 'merchantNotFound');

    }

    public function sendNotification($merchantPayment, $feesLimit)
    {
        // Send mail to admin
        (new NotifyAdminOnPaymentMailService())->send($merchantPayment, ['type' => 'payment', 'medium' => 'email', 'fee_bearer' => $this->feeBearer, 'fee' => $feesLimit['totalFee']]);

        // Send mail to merchant
        (new NotifyMerchantOnPaymentMailService())->send($merchantPayment, ['fee_bearer' => $this->feeBearer, 'fee' => $feesLimit['totalFee']]);
        return true;
    }


    public function generateSuccessUrl($merchantPayment, $url)
    {
        $response = [
            'status'         => 200,
            'transaction_id' => $merchantPayment->uuid,
            'merchant'       => getColumnValue($merchantPayment->merchant?->user),
            'currency'       => $merchantPayment->currency?->code,
            'fee'            => $merchantPayment->charge_percentage,
            'amount'         => $merchantPayment->amount,
            'total'          => $merchantPayment->total,
        ];
        $response = json_encode($response);
        $encodedResponse = base64_encode($response);

        return $url . '?' . $encodedResponse;
    }


    public function merchantPayment($feesLimit)
    {
        $merchantPayment                    = new MerchantPayment();
        $merchantPayment->merchant_id       = $this->merchantId;
        $merchantPayment->currency_id       = $this->currencyId;
        $merchantPayment->payment_method_id = $this->paymentMethodId;
        $merchantPayment->user_id           = $this->payerId ?? null;
        $merchantPayment->gateway_reference = $this->uniqueCode;
        $merchantPayment->order_no          = '';
        $merchantPayment->item_name         = '';
        $merchantPayment->uuid              = $this->uniqueCode;
        $merchantPayment->status            = 'Success';
        $merchantPayment->fee_bearer        = $this->feeBearer;
        $merchantPayment->charge_percentage = $feesLimit['depositPercent'] + $feesLimit['merchantPercentOrTotalFee'];
        $merchantPayment->charge_fixed      = $feesLimit['chargeFixed'];
        $merchantPayment->percentage        = $this->fee + $feesLimit['chargePercentage'];
        $merchantPayment->amount            = $this->feeBearer == 'Merchant' ? $this->amount - $feesLimit['totalFee'] : $this->amount;
        $merchantPayment->total             = $merchantPayment->amount + $feesLimit['totalFee'];
        $merchantPayment->save();
        return $merchantPayment;
    }

    public function createTransaction($merchantPaymentId, $feesLimit, $transactionType)
    {
        $transaction = new Transaction();
        $transaction->merchant_id = $this->merchantId;
        $transaction->currency_id = $this->currencyId;
        $transaction->payment_method_id = $this->paymentMethodId; 
        $transaction->uuid = $this->uniqueCode;
        $transaction->transaction_reference_id = $merchantPaymentId;
        $transaction->transaction_type_id = $transactionType;
        $transaction->status = 'Success';
        
        if ($transactionType == Payment_Sent) {
            $transaction->end_user_id = $this->userId;
            $transaction->user_id = $this->payerId;
            $transaction->subtotal = $this->amount;
            $transaction->percentage = $this->feeBearer == 'Merchant' ? 0 : $feesLimit['chargePercentage'] + $this->fee;
            $transaction->charge_percentage = $this->feeBearer == 'Merchant' ? 0 : $feesLimit['depositPercent'] + $feesLimit['merchantPercentOrTotalFee'];
            $transaction->charge_fixed = $this->feeBearer == 'Merchant' ? 0 : $feesLimit['chargeFixed'];
            $transaction->total = '-' . ($transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed);
        } elseif ($transactionType == Payment_Received) {
            $transaction->end_user_id = $this->payerId;
            $transaction->user_id = $this->userId;
            $transaction->subtotal = $this->feeBearer == 'Merchant' ? $this->amount - $feesLimit['totalFee'] : $this->amount;
            $transaction->percentage = $this->feeBearer == 'Merchant' ? $feesLimit['chargePercentage'] + $this->fee : 0;
            $transaction->charge_percentage = $this->feeBearer == 'Merchant' ? $feesLimit['depositPercent'] + $feesLimit['merchantPercentOrTotalFee'] : 0;
            $transaction->charge_fixed = $this->feeBearer == 'Merchant' ? $feesLimit['chargeFixed'] : 0;
            $transaction->total = $transaction->subtotal + $transaction->charge_percentage + $transaction->charge_fixed;
        }
        
        $transaction->save();
        return $transaction->id;
    }

    public function merchantWalletUpdate($feesLimit)
    {
       $merchantWallet = Wallet::where(['user_id' => $this->userId, 'currency_id' => $this->currencyId])->first(['id', 'balance']);
       if (empty($merchantWallet)) {
           $wallet              = new Wallet();
           $wallet->user_id     = $this->userId;
           $wallet->currency_id = $this->currencyId;
           $wallet->balance     = $this->feeBearer == 'Merchant' ? ($this->amount - $feesLimit['totalFee']) : $this->amount;
           $wallet->is_default  = 'No';
           $wallet->save();
       } else {
           $merchantWallet->balance = $this->feeBearer == 'Merchant' ? $merchantWallet->balance + ($this->amount - $feesLimit['totalFee']) : $merchantWallet->balance + $this->amount;
           $merchantWallet->save();
       }

       return true;

    }

    public function senderWalletOperation($feesLimit)
    {
        $senderWallet = Wallet::where(['user_id' => $this->payerId, 'currency_id' => $this->currencyId])->first(['id', 'balance']);

        self::checkNullAndThrowException($senderWallet, __('Sender wallet not found.'), 'merchantWalletNotFound');

        if ($senderWallet->balance < $this->amount) {
            self::throwException(__('Sender does not have enough balance.'), 'notHaveEnoughBalance');
        }
        //Check User has the wallet or not
        $senderWallet->balance = $this->feeBearer == 'Merchant' ? $senderWallet->balance - $this->amount : $senderWallet->balance - ($this->amount + $feesLimit['totalFee']) ;
        $senderWallet->save();
    }

    /**
     * checkNullAndThrowException
     *
     * @param object|int|null $object
     * @param string $message
     * @param string|null $reason
     *
     * @return void
     * 
     * @through ExpressMerchantPaymentException
     */
    public function checkNullAndThrowException(object|int|null $object, string $message, string|null $reason)
    {
        if (is_null($object)) {
            throw new ExpressMerchantPaymentException($message, [
                "reason" => $reason,
                "message" => $message
            ]);
        }
    }

    /**
     * Method throwException
     *
     * @param string $message
     * @param string|null $reason
     *
     * @return void
     * 
     * @through ExpressMerchantPaymentException
     */
    public function throwException(string $message, string|null $reason)
    {
        throw new ExpressMerchantPaymentException($message, [
            "reason" => $reason,
            "message" => $message
        ]);
    }
}