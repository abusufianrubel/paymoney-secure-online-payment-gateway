<?php

/**
 * @package FlutterwaveProcessor
 * @author tehcvillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 03-03-2025
 */

namespace App\Services\Gateways\Flutterwave;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Services\Gateways\Gateway\PaymentProcessor;
use App\Models\{
    User,
    Wallet,
    Deposit,
    Currency,
    Transaction,
    MerchantPayment,
    CurrencyPaymentMethod
};
use App\Services\Gateways\Gateway\Exceptions\{
    GatewayInitializeFailedException,
    PaymentFailedException
};

class FlutterwaveProcessor extends PaymentProcessor
{
    protected $data;

    protected $flutterwave;

    protected $baseurl;

    protected $accessToken;

    protected $supportedCurrencies = ['GBP', 'CAD', 'XAF', 'CLP', 'COP', 'EGP', 'EUR', 'GHS', 'GNF', 'KES', 'MWK', 'MAD', 'NGN', 'RWF', 'SLL', 'STD', 'ZAR', 'TZS', 'UGX', 'USD', 'XOF', 'ZMW'];

    /**
     * Boot flutterwave payment processor
     *
     * This method is used to initialize the flutterwave payment processor
     *
     * @param array $data
     *
     * @return void
     */
    protected function boot($data)
    {
        // Set the data
        $this->data = $data;

        // Set the payment currency
        $this->paymentCurrency();

        // Set the flutterwave credentials
        $this->flutterwave = $this->paymentMethodCredentials();

        // Check if the credentials are valid
        if (!$this->flutterwave->secret_key || !$this->flutterwave->public_key) {
            throw new GatewayInitializeFailedException(__("Flutterwave initialize failed."));
        }

        // check if the currency is supported
        if (!in_array($this->currency, $this->supportedCurrencies)) {
            throw new GatewayInitializeFailedException(__("Flutterwave does not support this currency."));
        }

        // Set the baseurl
        $this->baseurl = 'https://api.flutterwave.com/v3';

        // Set the access token
        $this->accessToken = $this->flutterwave->secret_key;
    }


    /**
     * Confirm payment for Flutterwave
     *
     * This method is used to confirm a payment for Flutterwave
     *
     * @param array $data
     *
     * @return array
     *
     * @throws PaymentFailedException
     */
    public function pay(array $data): array
    {
        try {
            // Boot the payment processor
            $this->boot($data);

            // Validate the payment request
            $this->validatePaymentRequest($data);

            // Check if the transaction already exists
            isTransactionExist($data['uuid']);

            // Create the payment link
            $paymentLink = $this->createPaymentLink();

            // Create the transaction
            $this->createTransaction();

            // Return a success message
            return [
                "action" => "success",
                "message" => __("Payment Link Created."),
                "type" => 'flutterwave',
                "redirect" => true,
                "redirect_url" => $paymentLink
            ];
        } catch (Exception $th) {
            // Throw a payment failed exception if there was an error
            throw new PaymentFailedException($th->getMessage());
        }
    }

    /**
     * Validate payment request
     *
     * This method is used to validate the payment request.
     * It checks if the required fields are present and
     * if the values are valid.
     *
     * @param array $data
     *
     * @return array
     */
    private function validatePaymentRequest(array $data): array
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:1'],
            'currency_id' => ['required', 'numeric'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ];

        // Validate the data
        return $this->validateData($data, $rules);
    }

    /**
     * Create a payment link
     *
     * This function creates a payment link using the Flutterwave API.
     * It sends a POST request with the necessary headers and request body.
     * If the response indicates an error, an exception is thrown.
     * Otherwise, the payment link is returned.
     *
     * @return string The payment link URL.
     *
     * @throws GatewayInitializeFailedException If the API response indicates an error.
     */
    private function createPaymentLink()
    {
        // Construct the URL for the Flutterwave payments endpoint
        $url = $this->baseurl . '/payments';

        // Send a POST request to the Flutterwave API with headers and request body
        $response = Http::withHeaders($this->getHeaders())->post($url, $this->getRequestBody());

        // Decode the JSON response
        $response = json_decode($response);

        // Check if the response indicates an error
        if ($response->status == 'error') {
            // Throw an exception with the error message
            throw new GatewayInitializeFailedException($response->message);
        }

        // Return the payment link from the response data
        return $response->data->link;
    }

    /**
     * Create a transaction
     *
     * This method creates a transaction and redirects the user to the
     * payment gateway.
     *
     * @return void
     *
     */
    private function createTransaction()
    {
        // Get the payment params
        $data = getPaymentParam($this->data['params']);

        // Call the API to create the transaction and redirect the user
        callAPI(
            'GET',
            $data['redirectUrl'],
            [
                'params' => $this->data['params'],
                'execute' => 'api'
            ]
        );
    }

    /**
     * Get the headers for the API requests
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken
        ];
    }

    /**
     * Prepares the request body for initiating a Flutterwave payment.
     *
     * Retrieves payment parameters and user information to construct the request payload.
     * Throws an exception if the user is not found.
     *
     * @return array The request body containing transaction reference, amount, currency,
     *               redirect URL, customer details, and customizations.
     *
     * @throws GatewayInitializeFailedException if the user is not found.
     */
    private function getRequestBody()
    {
        $data = getPaymentParam($this->data['params']);

        // Construct the request payload
        $payload = [
            "tx_ref" => $data['uuid'],
            "amount" => $data['totalAmount'],
            "currency" => $data['currencyCode'],
            "redirect_url" => url('gateway/payment-verify/flutterwave?params=' . $this->data['params']),
            "customizations" => [
                "title" => "Flutterwave Deposit Payment"
            ]
        ];

        // If the user ID is provided, retrieve the user details
        if (isset($data['user_id'])) {
            $user = User::find($data['user_id']);

            // Throw an exception if the user is not found
            if (empty($user)) {
                throw new GatewayInitializeFailedException(__("User not found."));
            }

            // Add the user details to the request payload
            $payload['customer'] = [
                "email" => $user->email,
                "name" => getColumnValue($user)
            ];
        } else {
            // If the user ID is not provided, use the email and name from the request
            $payload['customer'] = [
                "email" => request()->email,
                "name" => request()->name
            ];
        }

        return $payload;
    }

    /**
     * Returns the view for this payment method
     *
     * @return string
     */
    public function paymentView()
    {
        return 'gateways.' . $this->gateway();
    }


    /**
     * Get gateway alias name
     *
     * @return string
     */
    public function gateway(): string
    {
        return "flutterwave";
    }


    /**
     * Validate initialization request
     *
     * @param array $data
     *
     * @return array
     */
    private function validateInitiatePaymentRequest($data)
    {
        $rules = [
            'amount' => ['required'],
            'currency_id' => ['required'],
            'payment_method' => ['required', 'exists:payment_methods,id'],
            'redirectUrl' => ['required'],
            'transaction_type' => ['required'],
            'payment_type' => ['required']
        ];

        return $this->validateData($data, $rules);
    }

    /**
     * Initialize a Flutterwave payment
     *
     * This method validates the payment request and sets the payment type.
     * It then returns the view for the Flutterwave payment gateway.
     *
     * @param array $data
     *            The payment request data
     *
     * @return \Illuminate\Contracts\View\View
     *         The view for the Flutterwave payment gateway
     */
    public function initiateGateway($data)
    {
        // Set the payment type
        $this->setPaymentType($data['payment_type']);

        // Validate the payment request
        $this->validateInitiatePaymentRequest($data);

        // Return the view for the Flutterwave payment gateway
        return view(('gateways.' . $this->gateway()), $data);
    }

    /**
     * Verify a Flutterwave payment
     *
     * This method verifies a Flutterwave payment by capturing the payment
     * and updating the transaction and user wallet.
     *
     * @param Request $request
     *
     * @return array
     * @throws GatewayInitializeFailedException
     */
    public function verify($request)
    {
        try {

            $data = getPaymentParam($request->params);

            if ($request->status == 'cancelled') {
                $this->transactionUpdate('Blocked', $data['uuid']);
                throw new GatewayInitializeFailedException(__('You have cancelled your payment'));
            }

            if ($request->status != 'successful') {
                throw new GatewayInitializeFailedException(__("Flutterwave Payment failed."));
            }


            $data['payment_method_id'] = Flutterwave;
            $this->setPaymentType($data['payment_type']);
            $this->boot($data);
            $paymentCapture = $this->capturePayment($request->transaction_id);

            if ($paymentCapture->status == 'error') {
                throw new GatewayInitializeFailedException($paymentCapture->message);
            }

            if (
                isset($paymentCapture->data?->status) && $paymentCapture->data?->status == 'successful' &&
                $paymentCapture->data?->currency == $data['currencyCode'] && $paymentCapture->data?->amount == $data['totalAmount']
            ) {

                // Update the transaction and user wallet
                $this->transactionUpdate('Success', $data['uuid']);

                $transaction = Transaction::where('uuid', $data['uuid'])->first();
                $data['transaction_id'] = $transaction->id;
                return $data;
            }
        } catch (Exception $e) {
            throw new GatewayInitializeFailedException(__($e->getMessage()));
        }
    }

    /**
     * Capture a Flutterwave payment
     *
     * @param string $transactionId
     *
     * @return array
     */
    public function capturePayment($transactionId)
    {
        $url = $this->baseurl . '/transactions/' . $transactionId . '/verify';

        $response = Http::withHeaders($this->getHeaders())->get($url);

        return json_decode($response);
    }

    /**
     * Handle the Flutterwave payment callback
     *
     * This method is called when the Flutterwave payment gateway sends a callback
     * to the server. It is used to update the transaction status and user wallet
     * balance.
     *
     * @return void
     */
    public function processPayment()
    {
        // Get the payment data from the request body
        $data = json_decode(request()->getContent());

        // Check if the payment status is 'charge.completed' If it is not, exit the method
        if ($data->status != 'charge.completed') {
            return;
        }

        // Get the currency
        $currency = Currency::where('code', $data->data?->currency)->first();

        // Check if the currency is found If it is not, exit the method
        if (empty($currency)) {

            return;
        }

        // Get the payment method
        $paymentMethod = CurrencyPaymentMethod::query()
            ->where([
                'currency_id' => $currency->id,
                'method_id' => Flutterwave,
            ])
            ->where('activated_for', 'like', "%deposit%")
            ->first(['method_data']);

        // Check if the payment method is found If it is not, exit the method
        if (empty($paymentMethod)) {
            return;
        }

        // Decode the payment method data
        $paymentMethod = json_decode($paymentMethod->method_data);

        // Get the secret hash from the payment method data
        $secretHash = $paymentMethod->secret_hash;

        // Check if the secret hash is not empty
        if (!empty($secretHash)) {
            // Get the signature from the request headers
            $signature = request()->header('verif-hash');

            // Check if the signature is not empty and if it matches the secret hash
            // If it does not match, exit the method
            if (!$signature || ($signature !== $secretHash)) {
                return;
            }
        }

        // Map the payment status to the transaction status
        $status = [
            'successful' => 'Success',
            'pending' => 'Pending',
            'failed' => 'Blocked',
            'cancelled' => 'Blocked'
        ];

        // Find the transaction
        $transaction = Transaction::where([
            'uuid' => request()->tx_ref,
            'status' => 'Pending'
        ])->first();

        // Check if the transaction is found
        if (empty($transaction)) {
            // If it is not, exit the method
            return;
        }

        // Check if the currency ID and amount match the transaction
        if ($currency->id != $transaction->currency_id || $data->data?->amount == $transaction->total) {
            // If it does not match, exit the method
            return;
        }

        // Update the transaction status
        $transaction->status = $status[$data->data?->status];
        $transaction->save();

        // Update the deposit/merchant payment status to 'Success'
        if ($transaction->transaction_type_id == Deposit) {
            Deposit::updateStatus($transaction->transaction_reference_id, $status[$data->data?->status]);
        } else {
            MerchantPayment::where('uuid', $data['uuid'])->update(['status' => $status[$data->data?->status]]);
        }

        // Check if the transaction status is 'Success'
        if ($transaction->status == 'Success') {
            // Update the user wallet balance
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $transaction->user_id, 'currency_id' => $transaction->currency_id],
                ['balance' => 0]
            );

            $wallet->balance += $transaction->subtotal;
            $wallet->save();

            // Return a 200 response
            return response(200);
        }
    }

    public function transactionUpdate($status = 'Success', $uuid)
    {
        return Transaction::webHookTransactionUpdate($uuid, $status);
    }
}
