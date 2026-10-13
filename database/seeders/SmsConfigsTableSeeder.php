<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsConfigsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_configs')->delete();
        
        DB::table('sms_configs')->insert([
            [
                'type' => 'twilio',
                'credentials' => '',
                'status' => 'Inactive',
                'created_at' => Carbon::now()->subDays(700),
                'updated_at' => Carbon::now()->subDays(700),
            ],
            [
                'type' => 'nexmo',
                'credentials' => '',
                'status' => 'Inactive',
                'created_at' => Carbon::now()->subDays(700),
                'updated_at' => Carbon::now()->subDays(700),
            ],
        ]);
    }
}
