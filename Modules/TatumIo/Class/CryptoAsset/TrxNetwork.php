<?php

namespace Modules\TatumIo\Class\CryptoAsset;

use App\Models\Currency;
use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Modules\TatumIo\Entities\CryptoToken;
use Modules\TatumIo\Interfaces\NetworkInterface;

class TrxNetwork implements NetworkInterface
{
    protected $sdk;
    protected $network;
    protected $apiKey;

    public function __construct($apiKey, $network)
    {
        $this->sdk = new \Tatum\Sdk($apiKey);
        $this->network = $network;
        $this->apiKey = $apiKey;
    }

    public function generateWallet()
    {
        return $this->sdk->{$this->getEnvironment()}()
            ->api()
            ->tron()
            ->generateTronwallet();
    }

    public function generateAddress($xpub, $index)
    {
        return $this->sdk->{$this->getEnvironment()}()
            ->api()
            ->tron()
            ->tronGenerateAddress($xpub, $index);
    }

    public function generateAddressPrivateKey($index, $mnemonic)
    {
        $argPrivKeyRequest = (new \Tatum\Model\PrivKeyRequest())
            ->setIndex($index)
            ->setMnemonic($mnemonic);

        return $this->sdk->{$this->getEnvironment()}()
            ->api()
            ->tron()
            ->tronGenerateAddressPrivateKey($argPrivKeyRequest);
    }

    public function getBalanceOfAddress($address)
    {

        try {
            $url = tatumApiUrl('tron/account/' . $address);

            $response = Http::withHeaders([
                "x-api-key" => $this->apiKey,
            ])->get($url);

            if ($response->failed()) {
                $response = json_decode($response);
                if (isset($response->data[0])) {
                    throw new Exception($response->data[0]);
                }
                return 0;
            }
            $response = json_decode($response);

            return $response->balance * 0.000001;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getTransactionDetails($hash)
    {
        $response =  $this->sdk->{$this->getEnvironment()}()
            ->api()
            ->tron()
            ->tronGetTransaction($hash);

        return [
            'senderAddress' => $response['raw_data']['contract'][0]['parameter']['value']['ownerAddressBase58'],
            'receiverAddress' => $response['raw_data']['contract'][0]['parameter']['value']['toAddressBase58'],
            'networkFee' =>  $response['fee']  * 0.000001
        ];


    }

    public function getBlockChainInfo()
    {
        return $this->sdk->{$this->getEnvironment()}()
            ->api()
            ->tron()
            ->tronGetBlockChainInfo();
    }

    public function getAccount($address)
    {
        try {
            return $this->sdk->{$this->getEnvironment()}()
                ->api()
                ->tron()
                ->tronGetAccount($address);
        } catch (\Tatum\Sdk\ApiException $apiExc) {
            throw new Exception(__($apiExc->getResponseObject()['message']));
        } catch (\Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function getEstimateGasFees($from, $to, $amount)
    {
        return 0;
    }

    public function createTransaction($sender, $key, $receiver, $amount, $priority)
    {
        $url = tatumApiUrl('tron/transaction');
        $payload = [
            "fromPrivateKey" => $key,
            "to" => $receiver,
            "amount" => (string)$amount,
        ];

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "x-api-key" => $this->apiKey,
        ])->post($url, $payload);

        return json_decode($response);
    }

    private function getEnvironment()
    {
        return (!str_contains($this->network, 'TEST')) ? 'mainnet' : 'testnet';
    }

    public function deploySmartContract20($arr)
    {
        $url = tatumApiUrl('tron/trc20/deploy');
        $totalSupply = intval($arr['totalSupply']) * pow(10, intval($arr['decimals'])) ;

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            "x-api-key" => $this->apiKey,
        ])->post($url, [
            'fromPrivateKey' => $arr['privateKey'],
            'recipient' => $arr['receipent'],
            'name' => $arr['tokenName'],
            'symbol' => $arr['symbol'],
            'totalSupply' => $totalSupply,
            'decimals' => intval($arr['decimals'])
        ]);

       return json_decode($response);
        
    }

    public function getTokenList($address, $network)
    {
        $url = tatumApiUrl('tron/transaction/account/' . $address . '/trc20');
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey,
        ])->get($url);

        $response =   json_decode($response);

        if (isset($response->transactions)) {
            $this->adjustTokenList($response->transactions, $network, $address);
        }
    }

    public function adjustTokenList($transaction, $network, $address)
    {
        foreach ($transaction as $token) {

            if (!is_null(optional($token->tokenInfo)->address)) {
                $cryptoToken = CryptoToken::where('address', optional($token->tokenInfo)->address)
                                            ->orWhere('txId', $token->txID)->first();

                if (!$cryptoToken) {
                    $currency =  new \App\Models\Currency();
                    $currency->type = 'crypto_token';
                    $currency->name = optional($token->tokenInfo)->name;
                    $currency->symbol = optional($token->tokenInfo)->symbol;
                    $currency->code = strtoupper(optional($token->tokenInfo)->symbol);
                    $currency->status  ='Active';
                    $currency->save();
                }

                if (!$cryptoToken) {
                    $cryptoToken = new CryptoToken();
                    $cryptoToken->currency_id = $currency->id;
                }

                $cryptoToken->txid = $token->txID;
                $cryptoToken->network = $network;
                $cryptoToken->name = optional($token->tokenInfo)->name;
                $cryptoToken->symbol = optional($token->tokenInfo)->symbol;
                $cryptoToken->address = optional($token->tokenInfo)->address;
                $cryptoToken->decimals = optional($token->tokenInfo)->decimals;

                $cryptoToken->value = getTokenDecimalBalance(
                    $this->tokenDetails($address, optional($token->tokenInfo)->address), 
                    optional($token->tokenInfo)->decimals
                );

                $cryptoToken->status = 'Active';
                $cryptoToken->save();

            }
        }   
    }

    public function tokenDetails(string $address, string $tokenAddress)
    {
        $url = tatumApiUrl('tron/transaction/account/'.$address.'/trc20');
        $sendAmount = 0;
        $receiveAmount = 0;

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey,
        ])->get($url);

        if ($response->failed()) {
            return null; 
        }
        
        $data = $response->json();
        

        foreach($data['transactions'] as $transaction) {
            if (isset($transaction['tokenInfo']) 
                && isset($transaction['tokenInfo']['address']) 
                && $transaction['tokenInfo']['address'] == $tokenAddress 
                && $transaction['type'] == 'Transfer'
            ) {
                if ($transaction['from'] == $address) {
                    $sendAmount += $transaction['value'];
                }

                if ($transaction['to'] == $address) {
                    $receiveAmount +=  $transaction['value'];
                }
            }
            
        }

        return $receiveAmount - $sendAmount;

    }



    public function tokenTransfer($tokenAddress, $key, $receiver, $amount)
    {       
        $url = tatumApiUrl('tron/trc20/transaction');
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'x-api-key' => $this->apiKey,
        ])->post($url, [
            'fromPrivateKey' => $key, 
            'to' => $receiver, 
            'tokenAddress' => $tokenAddress, 
            'feeLimit' => 100,
            'amount' => $amount, 
        ]);

        return json_decode($response);
    }

    public function tokenTransactonDetails($txId)
    {
        $url = tatumApiUrl('tron/transaction/'.$txId);
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey, 
        ])->get($url);

        return json_decode($response);
    }


}
