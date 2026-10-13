<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
        
        DB::table('settings')->insert([
            [
                'name' => 'name',
                'type' => 'general',
                'value' => 'Pay Money',
            ],
            [
                'name' => 'logo',
                'type' => 'general',
                'value' => '1532175849_logo.png',
            ],
            [
                'name' => 'dark_logo',
                'type' => 'general',
                'value' => '1532175849_dark_logo.png',
            ],
            [
                'name' => 'favicon',
                'type' => 'general',
                'value' => '1530689937_favicon.png',
            ],
            [
                'name' => 'head_code',
                'type' => 'general',
                'value' => '',
            ],
            [
                'name' => 'default_currency',
                'type' => 'general',
                'value' => '1',
            ],
            [
                'name' => 'allowed_wallets',
                'type' => 'general',
                'value' => '2,3',
            ],
            [
                'name' => 'default_language',
                'type' => 'general',
                'value' => '1',
            ],
            [
                'name' => 'site_key',
                'type' => 'recaptcha',
                'value' => '',
            ],
            [
                'name' => 'secret_key',
                'type' => 'recaptcha',
                'value' => '',
            ],
            [
                'name' => 'default_timezone',
                'type' => 'general',
                'value' => 'Asia/Dhaka',
            ],
            [
                'name' => 'has_captcha',
                'type' => 'general',
                'value' => 'Disabled',
            ],
            [
                'name' => 'login_via',
                'type' => 'general',
                'value' => 'email_only',
            ],
            [
                'name' => 'admin_access_ip_setting',
                'type' => 'admin_security',
                'value' => 'Disabled',
            ],
            [
                'name' => 'admin_access_ips',
                'type' => 'admin_security',
                'value' => '::1',
            ],
            [
                'name' => 'exchange_enabled_api',
                'type' => 'currency_exchange_rate',
                'value' => 'Disabled',
            ],
            [
                'name' => 'currency_converter_api_key',
                'type' => 'currency_exchange_rate',
                'value' => null,
            ],
            [
                'name' => 'exchange_rate_api_key',
                'type' => 'currency_exchange_rate',
                'value' => null,
            ],
            [
                'name' => 'crypto_compare_enabled_api',
                'type' => 'crypto_compare_rate',
                'value' => 'Disabled',
            ],
            [
                'name' => 'crypto_compare_api_key',
                'type' => 'crypto_compare_rate',
                'value' => '',
            ]
        ]);
    }
}
