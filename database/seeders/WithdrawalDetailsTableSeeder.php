<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class WithdrawalDetailsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('withdrawal_details')->delete();
        
        DB::table('withdrawal_details')->insert([
            [
                'withdrawal_id' => getWithdrawalId('ZRVVKHGZILQS2'),
                'type' => getPaymentMethodId('Paypal'),
                'email' => 'kyla@gmail.com',
                'crypto_address' => null,
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(45),
            ],
            [
                'withdrawal_id' => getWithdrawalId('85TXMHQED6AD9'),
                'type' => getPaymentMethodId('Paypal'),
                'email' => 'irish@gmail.com',
                'crypto_address' => null,
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(35),
            ],
            [
                'withdrawal_id' => getWithdrawalId('HGRLFDAK7M55M'),
                'type' => getPaymentMethodId('Bank'),
                'email' => null,
                'crypto_address' => null,
                'account_name' => 'Kyla',
                'account_number' => '3253465',
                'bank_branch_name' => 'New York',
                'bank_branch_city' => 'New York',
                'bank_branch_address' => 'New York',
                'country' => getCountryId('US'),
                'swift_code' => '23423',
                'bank_name' => 'HSBC',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'withdrawal_id' => getWithdrawalId('8B1818D0732E6'),
                'type' => getPaymentMethodId('Paypal'),
                'email' => 'irish@gmail.com',
                'crypto_address' => null,
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'swift_code' => null,
                'bank_name' => null,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'withdrawal_id' => getWithdrawalId('D3DCDAF1ACA18'),
                'type' => getPaymentMethodId('Crypto'),
                'email' => null,
                'crypto_address' => '0x6BcA3DdEf6c42Cc8B741F8604b7cf7185f016782',
                'account_name' => null,
                'account_number' => null,
                'bank_branch_name' => null,
                'bank_branch_city' => null,
                'bank_branch_address' => null,
                'country' => null,
                'bank_name' => null,
                'swift_code' => null,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
        ]);
    }
}
