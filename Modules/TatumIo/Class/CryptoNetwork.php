<?php

namespace Modules\TatumIo\Class;

use App\Models\CryptoAssetSetting;
use Exception;
use Illuminate\Support\Facades\Http;
use Modules\TatumIo\Interfaces\NetworkInterface;

class CryptoNetwork implements NetworkInterface
{
    protected $sdk;
    protected $network;
    protected $apiKey;
    protected $networkProvider;

    public function __construct($apiKey, $network)
    {
        $this->apiKey = $apiKey;
        $this->network = $network;
        $this->setNetwork();
    }

    public function setNetWork()
    {
        $this->checkApiKey();
        $this->networkProvider = (new NetworkProvider())->getCryptoNetwork(str_replace('TEST', '', $this->network), $this->apiKey );
    }

    public function generateWallet()
    {
        return $this->networkProvider->generateWallet();
    }

    public function generateAddress($xpub, $index)
    {
        return $this->networkProvider->generateAddress($xpub, $index);
    }

    public function generateAddressPrivateKey($index, $mnemonic)
    {
        return $this->networkProvider->generateAddressPrivateKey($index, $mnemonic);
    }

    public function getBalanceOfAddress($address)
    {
        return $this->networkProvider->getBalanceOfAddress($address);
    }

    public function getBlockChainInfo()
    {
        return $this->networkProvider->getBlockChainInfo();
    }

    public function getEstimateFees($from, $to, $amount, $priority)
    {
        return $this->networkProvider->getEstimateGasFees($from, $to, $amount, $priority);
    }

    public function makeTransaction($sender, $key, $receiver, $amount, $priority)
    {
        return $this->networkProvider->createTransaction($sender, $key, $receiver, $amount, $priority);
    }

    public function tokenTransaction($tokenAddress, $key, $receiver, $amount)
    {
        return $this->networkProvider->tokenTransfer($tokenAddress, $key, $receiver, $amount);
    }

    public function transactionDetails($hash)
    {
        return $this->networkProvider->getTransactionDetails($hash);
    }



    private function getEnvironment()
    {
        return (!str_contains($this->network, 'TEST')) ? 'mainnet' : 'testnet';
    }

    public function networkName()
    {
        return [
            'BTC' => 'bitcoin',
            'BTCTEST' => 'bitcoin',
            'LTC' => 'litecoin',
            'LTCTEST' => 'litecoin',
            'DOGE' => 'dogecoin',
            'DOGETEST' => 'dogecoin',
            'ETH' => 'ethereum',
            'ETHTEST' => 'ethereum',
            'TRX' => 'tron',
            'TRXTEST' => 'tron'
        ][$this->network];
    }

    public function checkApiKey()
    {
        $url = tatumApiUrl('tatum/version');

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get($url);

        $response = json_decode($response);

        if (isset($response->statusCode)) {
            throw new Exception($response->message);
        }

        if ($response->testnet && $this->getEnvironment() == 'mainnet') {
            throw new Exception(__('The api key is for testnet environment, Your network should be like :x', ['x' => $this->network . 'TEST']));
        }

        if (!$response->testnet && $this->getEnvironment() == 'testnet') {
            throw new Exception(__('The api key is for mainnet environment Network'));
        }

        $tatumIoProvider = \App\Models\CryptoProvider::where('name', 'TatumIo')->orWhere('alias')->first();
        $accountInfoArr['current_plan'] = ucfirst($response->planName);
        $tatumIoProvider->subscription_details = json_encode($accountInfoArr);
        $tatumIoProvider->save();

        return true;
    }


    public function getTatumAssetSetting($status = 'Active', $selectOptions = ['*'])
    {
        $tatumAssetSetting = (new CryptoAssetSetting())->where('network', $this->network)->where(['payment_method_id' => TatumIo]);

        if ($status == 'Active') {
            return $tatumAssetSetting->where(['status' => 'Active'])->first($selectOptions);
        } elseif ($status == 'Inactive') {
            return $tatumAssetSetting->where(['status' => 'Inactive'])->first($selectOptions);
        } elseif ($status == 'All') {
            return $tatumAssetSetting->first($selectOptions);
        }
    }

    public function createSubscription($address)
    {
        $url = tatumApiUrl('subscription');

        $subscriptionUrl = route('tatumio.balance.notification');

        $network = str_replace('TEST', '', $this->network);
        $network = str_replace('TRX', 'TRON', $network);

        $payload = array(
            "type" => "ADDRESS_TRANSACTION",
            "attr" => array(
                "address" => $address,
                "chain" => str_replace('TEST', '', $network),
                "url" => $subscriptionUrl,
            ),
        );

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->post($url, $payload);

        return json_decode($response);
    }

    public function subscriptionList()
    {
        $url = tatumApiUrl('subscription?pageSize=30&offset=0');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "x-api-key:".$this->apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response);

            $data = collect($response);

            $coin = str_replace('TEST', '', $this->network);
            $coin = str_replace('TRX', 'TRON', $coin);


            return $data->filter(function ($item) use ($coin) {
                return $item->attr->chain === $coin;
            });
            
        }
    }

    public function deleteSubscription($subscriptionId)
    {
        $url = tatumApiUrl("subscription/" . $subscriptionId);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "x-api-key:".$this->apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } 
        
        return $response;
        
    }

    public function deploySmartContract($arr) 
    {
        return $this->networkProvider->deploySmartContract20($arr);
    }

    public function getSmartContractList($address, $network) 
    {
        return $this->networkProvider->getTokenList($address, $network);
    }

    public function getTokenDetails($address, $txId) 
    {
        return $this->networkProvider->tokenDetails($address, $txId);
    }

    public function tokenTransactionDetails($txId) 
    {
        return $this->networkProvider->tokenTransactonDetails($txId);
    }




}
