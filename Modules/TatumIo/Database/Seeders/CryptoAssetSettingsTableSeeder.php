<?php

namespace Modules\TatumIo\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CryptoAssetSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crypto_asset_settings')->delete();

        DB::table('crypto_asset_settings')->insert([
            [
                'currency_id' => getCurrencyId('LTCTEST'),
                'crypto_provider_id' => getCryptoProviderId('tatumio'),
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'network' => 'LTCTEST',
                'network_credentials' => '{"api_key":"t-655c302b6bff9e001cfcfefd-aa835038f54a4c3b970c8348","coin":"Litecoin","mnemonic":"bunker morning must visual muffin roast check until often mixture image again impact system nothing they legal cash bounce once audit anxiety board crumble","xpub":"ttub4gUshf67tc9fc7XTwUxgQXxEgyyQdHLtJBc75874MNeYvKUvE3A6tY56YEGxc9HxTzeYJs97wJ8jYBjBCjByKjd2HkZVg74eoREiNzLDuw8","key":"cSwXbspFEond8VK2KewCrGr7pMe3fR8yNQCT7S9x8bvy3sJ2wL8q","address":"muYDjZzr2DVx1iztQ8Z2LsUDNBjeEwGtPy","balance":100}',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(65),
                'updated_at' => Carbon::now()->subDays(65),
            ],
            [
                'currency_id' => getCurrencyId('BTCTEST'),
                'crypto_provider_id' => getCryptoProviderId('tatumio'),
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'network' => 'BTCTEST',
                'network_credentials' => '{"api_key":"t-655c302b6bff9e001cfcfefd-aa835038f54a4c3b970c8348","coin":"Bitcoin","mnemonic":"enlist whale harbor weapon erode oven risk file total grape mandate crop code sadness sign double month brown riot raw inner submit track clever","xpub":"tpubDEGA9buxFka7E2UsBv6xfFDsLujBc4CLzLJWrNfvwCtRzGCFzAiaFGjkCQbMVWZKvFXyFVA2qLiGL7TdWbroVQxCzFtMnFGbZudT3MXY2T9","key":"cW3hrfF5JhdgT8CNukJNqxFm83kjW5jm4cG8sD7KVVUA1qCVgxt5","address":"tb1q80m2tcl4s5nhq089h27qvrddg6s9hjyvw2vxqt","balance":5}',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(60),
            ],
            [
                'currency_id' => getCurrencyId('DOGETEST'),
                'crypto_provider_id' => getCryptoProviderId('tatumio'),
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'network' => 'DOGETEST',
                'network_credentials' => '{"api_key":"t-655c302b6bff9e001cfcfefd-aa835038f54a4c3b970c8348","coin":"Dogecoin","mnemonic":"process gold person cargo student length charge quality strike amused ugly category certain square jeans this minute gallery man dance margin payment surge critic","xpub":"tpubDFEnmGXcL5R3EXvoU7RhvVsK4Y2n3DDZq8T7gjL3FRN48MBkGvpxGTb6K7EnZjCyNGyy1Nh3nyeu868xpANJLXmpmCp9jPLVeXzndycuxP7","key":"cmpsD67NAYFaJTbwMmfuzKhf1yHPrzdnYB2zzSgntNDMRkhWMfdq","address":"ngMymGE6uBU2QQWMHZQMgjGggsyCFdHciU","balance":10000}',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(55),
                'updated_at' => Carbon::now()->subDays(55),
            ],
            [
                'currency_id' => getCurrencyId('TRXTEST'),
                'crypto_provider_id' => getCryptoProviderId('tatumio'),
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'network' => 'TRXTEST',
                'network_credentials' => '{"api_key":"t-655c302b6bff9e001cfcfefd-aa835038f54a4c3b970c8348","coin":"Tron","mnemonic":"spoil retreat lunar design guard thing earth symbol lobster example spot ritual offer process air boring patient pilot tower globe plug rather before museum","xpub":"xpub6EXAdwfj1bTyNo51iAS9GGkpqQLBQks3M8xjzzQxQae16dcLz7wWYNnJtyfL4j1UKMoRXPLg4xTME6pudgbZG4Xc1TK5CW9kZVLLpKMhGX3","key":"4a5fa1b3308b5965d8009408058e9c37e327d7a9624a4869cb3575da075a4ad4","address":"THJ9SGpnGo1weWynkM6rn8F8f1K2ecUp6h","balance":7000}',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(50),
            ]
        ]);
    }
}
