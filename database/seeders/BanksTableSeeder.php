<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->delete();
        
        DB::table('banks')->insert([
            [
                'user_id' => null,
                'admin_id' => getAdmin(),
                'currency_id' => getCurrencyId('USD'),
                'country_id' => getCountryId('US'),
                'file_id' => getFileId('1554634814.jpg'),
                'bank_name' => 'HSBC',
                'bank_branch_name' => 'New York',
                'bank_branch_city' => 'New York',
                'bank_branch_address' => 'New York',
                'account_name' => 'robiuzzaman parvez',
                'account_number' => '324346',
                'swift_code' => '213423',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(700),
                'updated_at' => Carbon::now()->subDays(700),
            ],
            [
                'user_id' => null,
                'admin_id' => getAdmin(),
                'currency_id' => getCurrencyId('USD'),
                'country_id' => getCountryId('US'),
                'file_id' => getFileId('1554634861.jpg'),
                'bank_name' => 'AIG',
                'bank_branch_name' => 'Washington DC',
                'bank_branch_city' => 'Washington DC',
                'bank_branch_address' => 'Washington DC',
                'account_name' => 'kyla watson',
                'account_number' => '345456723526236',
                'swift_code' => '67876',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(680),
                'updated_at' => Carbon::now()->subDays(680),
            ],
            [
                'user_id' => null,
                'admin_id' => getAdmin(),
                'currency_id' => getCurrencyId('USD'),
                'country_id' => getCountryId('AU'),
                'file_id' => null,
                'bank_name' => 'Alfala',
                'bank_branch_name' => 'Sydney',
                'bank_branch_city' => 'Sydney',
                'bank_branch_address' => 'Sydney',
                'account_name' => 'maria jane',
                'account_number' => '43536',
                'swift_code' => '234',
                'is_default' => 'No',
                'created_at' => Carbon::now()->subDays(670),
                'updated_at' => Carbon::now()->subDays(670),
            ],
            [
                'user_id' => null,
                'admin_id' => getAdmin(),
                'currency_id' => getCurrencyId('GBP'),
                'country_id' => getCountryId('US'),
                'file_id' => getFileId('1554640323.png'),
                'bank_name' => 'JPMorgan Chase',
                'bank_branch_name' => 'New York City',
                'bank_branch_city' => 'New York City',
                'bank_branch_address' => 'New York City',
                'account_name' => 'alfred stefano',
                'account_number' => '34543535348458wer',
                'swift_code' => '5465',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(660),
                'updated_at' => Carbon::now()->subDays(660),
            ],
            [
                'user_id' => null,
                'admin_id' => getAdmin(),
                'currency_id' => getCurrencyId('EUR'),
                'country_id' => getCountryId('BD'),
                'file_id' => getFileId('1689591463.png'),
                'bank_name' => 'Standard Chartered Bank',
                'bank_branch_name' => 'Gulshan Branch',
                'bank_branch_city' => 'Dhaka',
                'bank_branch_address' => '67 Gulshan Avenue, Dhaka 1212',
                'account_name' => 'John Doe',
                'account_number' => '2061059874563',
                'swift_code' => 'SCBLBDDX',
                'is_default' => 'Yes',
                'created_at' => Carbon::now()->subDays(640),
                'updated_at' => Carbon::now()->subDays(640),
            ],
        ]);
    }
}
