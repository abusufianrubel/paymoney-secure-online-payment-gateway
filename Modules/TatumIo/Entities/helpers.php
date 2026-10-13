<?php

use App\Models\CryptoAssetApiLog;
use Modules\TatumIo\Class\TatumIoTransaction;

if (!function_exists('updateTatumAssetCredentials')) {

    function updateTatumAssetCredentials()
    {
        try {
            $cryptoAssetSettings = \App\Models\CryptoAssetSetting::where('payment_method_id', TatumIo)->get();
            if (!empty($cryptoAssetSettings)) {
                $tatumIoNetworkArray = [];
                foreach ($cryptoAssetSettings as  $cryptoAssetSetting) {
                    if ($cryptoAssetSetting->currency->type == 'crypto_token') {
                        continue;
                    }
                    $network = $cryptoAssetSetting->network;
                    $tatumIo = new TatumIoTransaction($network) ?? 0;
                    $tatumIo->tatumIoAsset();
                    $balance = $tatumIo->getMerchantBalance();
                    $networkCredential = json_decode($cryptoAssetSetting->network_credentials);

                    $tatumIoNetworkArray['api_key'] = $networkCredential->api_key;
                    $tatumIoNetworkArray['coin'] = $networkCredential->coin;
                    $tatumIoNetworkArray['mnemonic'] = $networkCredential->mnemonic;
                    $tatumIoNetworkArray['xpub'] = $networkCredential->xpub;
                    $tatumIoNetworkArray['key'] = $networkCredential->key;
                    $tatumIoNetworkArray['address'] = $networkCredential->address;
                    $tatumIoNetworkArray['balance'] = $balance;
                    $cryptoAssetSetting->network_credentials = json_encode($tatumIoNetworkArray);
                    $cryptoAssetSetting->save();
                }
            }

            return ['status' => true];

        } catch (\Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage()];
        }

        
    }
}

if (!function_exists('getReceiverAddressWalletUserId')) {
    function getReceiverAddressWalletUserId($receiverAddress)
    {
        return  (new CryptoAssetApiLog())
        ->with(['wallet:id,user_id'])
        ->where(['payment_method_id' => TatumIo, 'object_type' => 'wallet_address'])
        ->whereJsonContains('payload->address', $receiverAddress )
        ->first('object_id');

    }
}

if (!function_exists('getProviderActiveStatus')) {

    function getProviderActiveStatus($providers)
    {
        $activeCryptoProviders = [];

        if (isset($providers)) {
            foreach ($providers as $cryptoProvider) {
                if (isActive($cryptoProvider->name)) {
                    $activeCryptoProviders[$cryptoProvider->alias] = true;
                }
            }
        }
        return $activeCryptoProviders;
    }
}

if (!function_exists('getTatumIoMinLimit')) {

    function getTatumIoMinLimit($type = null, $network = null)
    {
        $minLimit = [
            'amount' => [
                'BTC' => 0.00002,
                'BTCTEST' => 0.00002,
                'DOGE' => 2,
                'DOGETEST' => 2,
                'LTC' => 0.0002,
                'LTCTEST' => 0.0002,
                'ETH' => 0.000002,
                'ETHTEST' => 0.000002,
                'TRX' => 1,
                'TRXTEST' => 1,
            ],
            'networkFee' => [
                'BTC' => 0.0002,
                'BTCTEST' => 0.0002,
                'DOGE' => 1,
                'DOGETEST' => 1,
                'LTC' => 0.0001,
                'LTCTEST' => 0.0001,
                'ETH' => 0.0002,
                'ETHTEST' => 0.0002,
                'TRX' => 1,
                'TRXTEST' => 1,
            ],
        ];
        if (is_null($network) && is_null($network)) {
            return $minLimit;
        }
        return $minLimit[$type][$network];
    }
}

if (!function_exists('tatumGetCryptoTransactionApiLog')) { 

    function tatumGetCryptoTransactionApiLog($txId)
    {
        return (new CryptoAssetApiLog())
            ->with(['transaction:id'])
            ->where(['payment_method_id' => TatumIo])
            ->whereJsonContains('payload->txId', $txId)
            ->first();
    }
}

if (!function_exists('tatumGetWalletApiLog')) { 
    function tatumGetWalletApiLog($address)
    {
        return (new CryptoAssetApiLog())
            ->with(['wallet:id,user_id,currency_id'])
            ->where(['payment_method_id' => TatumIo, 'object_type' => 'wallet_address'])
            ->whereJsonContains('payload->address', $address)
            ->first();
    }
}


/**
 * Converts a token balance from its smallest unit to its actual value.
 *
 * @param int|float $value The token balance in the smallest unit.
 * @param int $decimals The number of decimal places the token supports.
 * @return float The actual token balance in a human-readable format.
 */
if (!function_exists('getTokenDecimalBalance')) { 
    function getTokenDecimalBalance($value , $decimals)
    {
        return $value / pow(10, $decimals);
    }
}

/**
 * Converts a token's actual value to its smallest unit for storage.
 *
 * The actual balance is multiplied by 10 raised to the power of the token's decimals.
 *
 * @param float $value The actual token balance in a human-readable format.
 * @param int $decimals The number of decimal places the token supports.
 * @return int|float The token balance in the smallest unit, ready for storage in the database.
 */
if (!function_exists('setTokenDecimalBalance')) { 

    function setTokenDecimalBalance($value , $decimals)
    {
        return $value * pow(10, $decimals);
    }
}

/**
 * Generates the full Tatum API URL for a given endpoint.
 *
 *
 * @param string $endpoint The API endpoint to append to the base URL.
 * @return string The complete API URL for the specified endpoint.
 */
if (!function_exists('tatumApiUrl')) { 

    function tatumApiUrl($endpoint) 
    {
        return config('tatumio.tatum_api_url') . '/' . config('tatumio.tatum_api_version'). '/'  . $endpoint;
    }
}

if (!function_exists('getTatumIoTransactionReference')) {
    function getTatumIoTransactionReference($objectType, $network)
    {
        return (new CryptoAssetApiLog())
            ->where(['object_type' => $objectType, 'network' => $network])
            ->first()
            ->id;
    }
}



