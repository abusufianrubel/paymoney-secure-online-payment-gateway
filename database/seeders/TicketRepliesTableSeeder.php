<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TicketRepliesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticket_replies')->delete();
        
        DB::table('ticket_replies')->insert([
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('irish@gmail.com'),
                'ticket_id' => getTicketId('TIC-E3PRAZ'),
                'user_type' => 'admin',
                'message' => 'Hello, Lorem ipsum dolor sit amet, consectetur adipiscing elit. In et augue leo. Aenean nec pharetra orci. Phasellus nec malesuada orci, vel hendrerit lorem. In nunc nunc, tristique eu augue quis, tempor laoreet dolor. Vivamus malesuada nisl in arcu pharetra finibus. Sed vitae ligula a magna dignissim rhoncus vel eget elit. Morbi malesuada lacus a sagittis sodales. Maecenas quis nisi sit amet justo luctus pellentesque at ut risus. In fringilla aliquam ipsum nec faucibus. Vestibulum sit amet commodo velit. Quisque tellus arcu, faucibus scelerisque viverra id, euismod at lectus. Vivamus arcu nibh, consectetur sit amet felis vel, convallis egestas massa. Aenean sapien risus, porttitor quis magna vel, vehicula dapibus nulla. Maecenas facilisis volutpat nunc. Thank you',
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => getTicketId('TIC-U0KKUZ'),
                'message' => 'Helllo,&nbsp;<div><br></div><div>You can not exchange it.&nbsp;<div><br></div><div>Thank you</div></div>',
                'user_type' => 'admin',
                'created_at' => Carbon::now()->subDays(75),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => getTicketId('TIC-FOPCTM'),
                'message' => 'hello..',
                'user_type' => 'admin',
                'created_at' => Carbon::now()->subDays(55),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => getTicketId('TIC-FOPCTM'),
                'message' => 'Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes,',
                'user_type' => 'user',
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_id' => getTicketId('TIC-FOPCTM'),
                'message' => 'Hi, Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor.',
                'user_type' => 'admin',
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => null,
            ],
        ]);
    }
}
