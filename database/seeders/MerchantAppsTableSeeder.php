<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantAppsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchant_apps')->delete();
        
        DB::table('merchant_apps')->insert([
            [
                'client_id' => 'tzEuYcDZfPv1f0ax9WFfzWbjYxslwy',
                'client_secret' => 'Ygpkf7TEMIUw6ToM5Ik5y2DP46DMSYQQ0trm82KEYHtWsnMH69Uw0UgJvkbFYZyKTjbekcMzpzfgw9nkc0l3iZngzJhMimDm1Hbk',
                'merchant_id' => getExpressMerchantId(),
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ]);
    }
}
