<?php

namespace Modules\TatumIo\Traits;

use App\Models\CryptoProvider;
use App\Models\CryptoAssetSetting;
use Exception;
use Modules\TatumIo\Class\TatumIoTransaction;

trait TokenTrait
{
    // Method to return the supported networks
    protected function getSupportedNetworks()
    {
        return ['TRX', 'TRXTEST'];
    }

    public function tokenSupportedNetwork()
    {
        $cryptoProviderId = CryptoProvider::where('alias', 'tatumio')
                                          ->value('id');

        if (!$cryptoProviderId) {
            return collect(); 
        }

        return CryptoAssetSetting::where('crypto_provider_id', $cryptoProviderId)
                                ->where('status', 'Active')
                                 ->whereIn('network', $this->getSupportedNetworks())
                                 ->pluck('network');
    }

    public function supportedNetworkAddress()
    {   
        $supportedNetworks = $this->tokenSupportedNetwork();
        $address = [];
        foreach ($supportedNetworks as $key => $network) {
            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
           $address[$key] =  $tatumIo->getMerchantAddress();  
        }
        return $address;
    }



    public function createTatumIoERC20Token($request)
    {
        try {
            $tatumIo = new TatumIoTransaction($request->network);
            $tatumIo->tatumIoAsset();
            if($tatumIo->getMerchantBalance() < 500) {
                throw new Exception(__('Not have Enough :x balance for token deployment fees', ['x' => $request->network ]));
            }
            $response =  $tatumIo->generateSmartContract(
                $request->name, 
                $request->symbol, 
                $request->decimals, 
                $request->total_supply
            ); 

            if (isset($response->txId)) {
                return $response->txId;
            }

            throw new Exception(__('Token Deployment Failed'));
            
        } catch (Exception $e) {
            throw new Exception(__($e->getMessage()));
        }
         
    }

    public function getAllToken()
    {
        $networks = $this->tokenSupportedNetwork();

        foreach ($networks as $network) {
            $tatumIo = new TatumIoTransaction($network);
            $tatumIo->tatumIoAsset();
            $tatumIo->getSmartContractList($network);
        }

    }

}