<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CurrencyExchangesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency_exchanges')->delete();
        
        DB::table('currency_exchanges')->insert([
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('GBP')),
                'currency_id' => getCurrencyId('GBP'),
                'uuid' => 'OODBEVGDJG1O9',
                'exchange_rate' => '0.77000000',
                'amount' => '5.00000000',
                'fee' => '3.00000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => Carbon::now()->subDays(80),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('GBP')),
                'currency_id' => getCurrencyId('GBP'),
                'uuid' => '9SMHGSIGJFROE',
                'exchange_rate' => '0.85000000',
                'amount' => '10.00000000',
                'fee' => '3.25000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(65),
                'updated_at' => Carbon::now()->subDays(65),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('EUR')),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => 'MXABDSNUGLDL6',
                'exchange_rate' => '0.85000000',
                'amount' => '5.00000000',
                'fee' => '3.13000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(55),
                'updated_at' => Carbon::now()->subDays(55),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('EUR')),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => '2MV5FASLHOJCF',
                'exchange_rate' => '10.00000000',
                'amount' => '10.00000000',
                'fee' => '1.01000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(35),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('EUR')),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => 'PZT7JCT2Q6E60',
                'exchange_rate' => '10.00000000',
                'amount' => '155.00000000',
                'fee' => '1.19000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('GBP')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('EUR')),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => '2C5AF6170627D',
                'exchange_rate' => '1.13333333',
                'amount' => '75.00000000',
                'fee' => '2.20000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'from_wallet' => getCurrencyExchangeWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('USD')),
                'to_wallet' => getCurrencyExchangeWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('EUR')),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => '6E58B0B6B61A7',
                'exchange_rate' => '0.85000000',
                'amount' => '100.00000000',
                'fee' => '1.12000000',
                'type' => 'Out',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ]);
    }
}
