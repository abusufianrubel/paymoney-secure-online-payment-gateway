<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutSettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('payout_settings')->delete();
        
        DB::table('payout_settings')->insert([
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'type' => getPaymentMethodId('Paypal'),
                'email' => 'kyla@gmail.com',
                'currency_id' => null,
                'crypto_address' => null,
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'default_payout' => 0,
                'created_at' => Carbon::now()->subDays(95),
                'updated_at' => Carbon::now()->subDays(95),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'type' => getPaymentMethodId('Paypal'),
                'email' => 'irish@gmail.com',
                'currency_id' => null,
                'crypto_address' => null,
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'default_payout' => 0,
                'created_at' => Carbon::now()->subDays(85),
                'updated_at' => Carbon::now()->subDays(85),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'type' => getPaymentMethodId('Bank'),
                'email' => null,
                'currency_id' => null,
                'crypto_address' => null,
                'account_name' => 'Kyla',
                'account_number' => '3253465',
                'bank_branch_name' => 'New York',
                'bank_branch_city' => 'New York',
                'bank_branch_address' => 'New York',
                'country' => getCountryId('US'),
                'bank_name' => 'HSBC',
                'swift_code' => '23423',
                'default_payout' => 0,
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => Carbon::now()->subDays(90),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'type' => getPaymentMethodId('Crypto'),
                'email' => null,
                'currency_id' => getCurrencyId('ETH'),
                'crypto_address' => '0x6BcA3DdEf6c42Cc8B741F8604b7cf7185f016782',
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'default_payout' => 0,
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => Carbon::now()->subDays(80),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'type' => getPaymentMethodId('Bank'),
                'email' => null,
                'currency_id' => null,
                'crypto_address' => null,
                'account_name' => 'Sandha Proud',
                'account_number' => '845698247-C',
                'bank_branch_name' => 'al Bank',
                'bank_branch_city' => 'Victoria Island',
                'bank_branch_address' => '635, Akin Adesola Street, Victoria Island, Lagos',
                'country' => getCountryId('NG'),
                'swift_code' => 'GLBSNGLAS019',
                'bank_name' => 'Globus Bank Limited',
                'default_payout' => 0,
                'created_at' => Carbon::now()->subDays(75),
                'updated_at' => Carbon::now()->subDays(75),
            ],
        ]);
    }
}
