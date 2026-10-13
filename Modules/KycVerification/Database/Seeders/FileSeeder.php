<?php

namespace Modules\KycVerification\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (checkDemoEnvironment()) {
            DB::table('files')->insert(
                [
                    [
                        'admin_id' => null,
                        'user_id' => getUserByEmail('irish@gmail.com'),
                        'ticket_id' => null,
                        'ticket_reply_id' => null,
                        'filename' => '1688456042.png',
                        'originalname' => 'passport.png',
                        'type' => 'png',
                        'created_at' => Carbon::now()->subDays(200),
                        'updated_at' => null,
                    ],
                    [
                        'admin_id' => null,
                        'user_id' => getUserByEmail('irish@gmail.com'),
                        'ticket_id' => null,
                        'ticket_reply_id' => null,
                        'filename' => '1688456225.png',
                        'originalname' => 'address.png',
                        'type' => 'png',
                        'created_at' => Carbon::now()->subDays(180),
                        'updated_at' => null,
                    ],
                ]
            );
        }
    }
}
