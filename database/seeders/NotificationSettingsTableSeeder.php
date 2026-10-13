<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('notification_settings')->delete();
        
        \DB::table('notification_settings')->insert([
            [
                'notification_type_id' => getNotificationTypeId('deposit'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
            [
                'notification_type_id' => getNotificationTypeId('payout'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
            [
                'notification_type_id' => getNotificationTypeId('send'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
            [
                'notification_type_id' => getNotificationTypeId('request'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
            [
                'notification_type_id' => getNotificationTypeId('exchange'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
            [
                'notification_type_id' => getNotificationTypeId('payment'),
                'recipient_type' => 'email',
                'recipient' => 'admin@techvill.net',
                'status' => 'Yes',
                'created_at' => Carbon::now()->subDays(590),
                'updated_at' => Carbon::now()->subDays(590),
            ],
        ]);
    }
}
