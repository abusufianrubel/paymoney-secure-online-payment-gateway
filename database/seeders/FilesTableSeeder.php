<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class FilesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('files')->delete();
        
        DB::table('files')->insert([
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => getTicketId('TIC-FOPCTM'),
                'ticket_reply_id' => \App\Models\TicketReply::where('ticket_id', getTicketId('TIC-FOPCTM'))->first()->id,
                'filename' => '1532341473.png',
                'originalname' => '1532341473.png',
                'type' => 'png',
                'created_at' => Carbon::now()->subDays(55),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => null,
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1554634814.jpg',
                'originalname' => '1551691713.jpg',
                'type' => 'jpg',
                'created_at' => Carbon::now()->subDays(700),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => null,
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1554634861.jpg',
                'originalname' => '1551691703.jpg',
                'type' => 'jpg',
                'created_at' => Carbon::now()->subDays(680),
                'updated_at' => null,
            ],
            [
                'admin_id' => null,
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1554635672.txt',
                'originalname' => 'test.txt',
                'type' => 'txt',
                'created_at' => Carbon::now()->subDays(95),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => null,
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1554640323.png',
                'originalname' => 'JPMorgan.png',
                'type' => 'png',
                'created_at' => Carbon::now()->subDays(660),
                'updated_at' => null,
            ],
            [
                'admin_id' => null,
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1554640558.jpg',
                'originalname' => 'Jellyfish.jpg',
                'type' => 'jpg',
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => null,
                'ticket_id' => null,
                'ticket_reply_id' => null,
                'filename' => '1689591463.png',
                'originalname' => 'Standard_Chartered_logo.png',
                'type' => 'png',
                'created_at' => Carbon::now()->subDays(640),
                'updated_at' => null,
            ],
        ]);
    }
}
