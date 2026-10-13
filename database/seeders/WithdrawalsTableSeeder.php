<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WithdrawalsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('withdrawals')->delete();
        
        DB::table('withdrawals')->insert([
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'payment_method_id' => getPaymentMethodId('Paypal'),
                'uuid' => 'ZRVVKHGZILQS2',
                'charge_percentage' => '0.16000000',
                'charge_fixed' => '1.00000000',
                'subtotal' => '78.84000000',
                'amount' => '80.00000000',
                'payment_method_info' => 'kyla@gmail.com',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(45),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'payment_method_id' => getPaymentMethodId('Paypal'),
                'uuid' => 'KREMU7KIZFPIU',
                'charge_percentage' => '0.04000000',
                'charge_fixed' => '1.00000000',
                'subtotal' => '18.96000000',
                'amount' => '20.00000000',
                'payment_method_info' => 'kyla@gmail.com',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(40),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'payment_method_id' => getPaymentMethodId('Paypal'),
                'uuid' => 'AXVQAOI9W5PWM',
                'charge_percentage' => '0.20000000',
                'charge_fixed' => '1.00000000',
                'subtotal' => '98.80000000',
                'amount' => '100.00000000',
                'payment_method_info' => 'kyla@gmail.com',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'payment_method_id' => getPaymentMethodId('Paypal'),
                'uuid' => '85TXMHQED6AD9',
                'charge_percentage' => '0.02000000',
                'charge_fixed' => '2.00000000',
                'subtotal' => '17.98000000',
                'amount' => '20.00000000',
                'payment_method_info' => 'irish@gmail.com',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(35),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'payment_method_id' => getPaymentMethodId('Bank'),
                'uuid' => 'HGRLFDAK7M55M',
                'charge_percentage' => '12.00000000',
                'charge_fixed' => '25.00000000',
                'subtotal' => '83.00000000',
                'amount' => '120.00000000',
                'payment_method_info' => 'Kyla',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'payment_method_id' => getPaymentMethodId('Paypal'),
                'uuid' => '8B1818D0732E6',
                'charge_percentage' => '0.80000000',
                'charge_fixed' => '2.30000000',
                'subtotal' => '46.90000000',
                'amount' => '50.00000000',
                'payment_method_info' => 'irish@gmail.com',
                'status' => 'Blocked',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('ETH'),
                'payment_method_id' => getPaymentMethodId('Crypto'),
                'uuid' => 'D3DCDAF1ACA18',
                'charge_percentage' => '0.00106000',
                'charge_fixed' => '0.00540000',
                'subtotal' => '0.04654000',
                'amount' => '0.05300000',
                'payment_method_info' => '0x6BcA3DdEf6c42Cc8B741F8604b7cf7185f016782',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
        ]);
    }
}
