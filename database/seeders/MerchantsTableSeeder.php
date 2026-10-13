<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchants')->delete();
        
        DB::table('merchants')->insert([
            [
                'user_id' => getUser('merchant'),
                'currency_id' => getCurrencyId('USD'),
                'merchant_group_id' => getDefaultMerchantGroupId(),
                'merchant_uuid' => 'X43BS17Y7PL81',
                'business_name' => 'Amazon',
                'site_url' => 'http://amazon.com',
                'type' => 'standard',
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut ',
                'logo' => '1532329093.jpg',
                'fee' => '1.50000000',
                'status' => 'Moderation',
                'created_at' => Carbon::now()->subDays(122),
                'updated_at' => Carbon::now()->subDays(122),
            ],
            [
                'user_id' => getUser('merchant'),
                'currency_id' => getCurrencyId('GBP'),
                'merchant_group_id' => getDefaultMerchantGroupId(),
                'merchant_uuid' => 'J7OJ4STR4ZMXJ',
                'business_name' => 'eBay',
                'site_url' => 'http://eBay.com',
                'type' => 'standard',
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut ',
                'logo' => '1532329129.png',
                'fee' => '1.50000000',
                'status' => 'Approved',
                'created_at' => Carbon::now()->subDays(91),
                'updated_at' => Carbon::now()->subDays(91),
            ],
            [
                'user_id' => getUser('merchant'),
                'currency_id' => getCurrencyId('USD'),
                'merchant_group_id' => getDefaultMerchantGroupId(),
                'merchant_uuid' => 'Z3IKX4CNC2ULK',
                'business_name' => 'Flipkart',
                'site_url' => 'http://www.flipkart.com',
                'type' => 'standard',
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut ',
                'logo' => '1532329173.png',
                'fee' => '1.50000000',
                'status' => 'Approved',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(60),
            ],
            [
                'user_id' => getUser('merchant'),
                'currency_id' => getCurrencyId('EUR'),
                'merchant_group_id' => getDefaultMerchantGroupId(),
                'merchant_uuid' => 'LDWOFPPW6YEOJ',
                'business_name' => 'Berger',
                'site_url' => 'http://berger.com',
                'type' => 'express',
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut',
                'logo' => '1532329245.png',
                'fee' => '1.50000000',
                'status' => 'Approved',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ]);
    }
}
