<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WalletsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('wallets')->delete();

        \DB::table('wallets')->insert([
            [
                'user_id' => getUserByEmail('john@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'balance' => '5.00000000',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'balance' => '1977.98000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(771),
                'updated_at' => Carbon::now()->subDays(771),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'balance' => '1638.99000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(772),
                'updated_at' => Carbon::now()->subDays(772),
            ],
            [
                'user_id' => getUserByEmail('nuzhat@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'balance' => '0.00000000',
                'is_default' => 'No',
                'created_at' => '2018-07-19 16:51:19',
                'updated_at' => '2018-07-23 18:48:11',
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'balance' => '49944.53000000',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(772),
                'updated_at' => Carbon::now()->subDays(772),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'balance' => '19847.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(772),
                'updated_at' => Carbon::now()->subDays(772),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'balance' => '115.83000000',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(771),
                'updated_at' => Carbon::now()->subDays(771),
            ],
            [
                'user_id' => getUserByEmail('john@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'balance' => '4000.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
            ],
            [
                'user_id' => getUserByEmail('nuzhat@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'balance' => '995.00000000',
                'is_default' => 'Yes',
                'created_at' => '2018-07-19 21:04:05',
                'updated_at' => '2018-07-23 18:48:11',
            ],
            [
                'user_id' => getUserByEmail('john@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'balance' => '10.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'balance' => '41.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(771),
                'updated_at' => Carbon::now()->subDays(771),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('LTCTEST'),
                'balance' => '0.00040000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(735),
                'updated_at' => Carbon::now()->subDays(735),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('BTCTEST'),
                'balance' => '0.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(735),
                'updated_at' => Carbon::now()->subDays(735),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('LTCTEST'),
                'balance' => '0.00228403',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(730),
                'updated_at' => Carbon::now()->subDays(730),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('BTCTEST'),
                'balance' => '0.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(730),
                'updated_at' => Carbon::now()->subDays(730),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('ETH'),
                'balance' => '0.61720000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(700),
                'updated_at' => Carbon::now()->subDays(700),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('DOGETEST'),
                'balance' => '1.74440939',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(690),
                'updated_at' => Carbon::now()->subDays(690),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('DOGETEST'),
                'balance' => '4.74192582',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(680),
                'updated_at' => Carbon::now()->subDays(680),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('TRXTEST'),
                'balance' => '31.57218',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(690),
                'updated_at' => Carbon::now()->subDays(690),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('TRXTEST'),
                'balance' => '0.00000000',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(670),
                'updated_at' => Carbon::now()->subDays(670),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('USDT'),
                'balance' => '88.855',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(650),
                'updated_at' => Carbon::now()->subDays(650),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USDT'),
                'balance' => '111.0302',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(630),
                'updated_at' => Carbon::now()->subDays(630),
            ],
        ]);
    }
}
