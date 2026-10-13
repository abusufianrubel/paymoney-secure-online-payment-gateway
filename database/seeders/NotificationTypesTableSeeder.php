<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('notification_types')->delete();
        
        \DB::table('notification_types')->insert([
            [
                'name' => 'Deposit',
                'alias' => 'deposit',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
            [
                'name' => 'Payout',
                'alias' => 'payout',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
            [
                'name' => 'Send',
                'alias' => 'send',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
            [
                'name' => 'Request',
                'alias' => 'request',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
            [
                'name' => 'Exchange',
                'alias' => 'exchange',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
            [
                'name' => 'Payment',
                'alias' => 'payment',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
            ],
        ]);
    }
}
