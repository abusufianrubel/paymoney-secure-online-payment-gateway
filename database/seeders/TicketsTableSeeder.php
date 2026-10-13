<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('tickets')->delete();
        
        DB::table('tickets')->insert([
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('irish@gmail.com'),
                'ticket_status_id' => getTicketStatusId('Open'),
                'subject' => 'New Ticket',
                'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In et augue leo. Aenean nec pharetra orci. Phasellus nec malesuada orci, vel hendrerit lorem. In nunc nunc, tristique eu augue quis, tempor laoreet dolor. Vivamus malesuada nisl in arcu pharetra finibus. Sed vitae ligula a magna dignissim rhoncus vel eget elit. Morbi malesuada lacus a sagittis sodales. Maecenas quis nisi sit amet justo luctus pellentesque at ut risus. In fringilla aliquam ipsum nec faucibus. Vestibulum sit amet commodo velit. Quisque tellus arcu, faucibus scelerisque viverra id, euismod at lectus. Vivamus arcu nibh, consectetur sit amet felis vel, convallis egestas massa. Aenean sapien risus, porttitor quis magna vel, vehicula dapibus nulla. Maecenas facilisis volutpat nunc.',
                'code' => 'TIC-E3PRAZ',
                'priority' => 'Low',
                'last_reply' => Carbon::now()->subDays(80),
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_status_id' => getTicketStatusId('Hold'),
                'subject' => 'Issues on product color',
                'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In et augue leo. Aenean nec pharetra orci. Phasellus nec malesuada orci, vel hendrerit lorem. In nunc nunc, tristique eu augue quis, tempor laoreet dolor. Vivamus malesuada nisl in arcu pharetra finibus. Sed vitae ligula a magna dignissim rhoncus vel eget elit. Morbi malesuada lacus a sagittis sodales. Maecenas quis nisi sit amet justo luctus pellentesque at ut risus. In fringilla aliquam ipsum nec faucibus. Vestibulum sit amet commodo velit. Quisque tellus arcu, faucibus scelerisque viverra id, euismod at lectus. Vivamus arcu nibh, consectetur sit amet felis vel, convallis egestas massa. Aenean sapien risus, porttitor quis magna vel, vehicula dapibus nulla. Maecenas facilisis volutpat nunc.',
                'code' => 'TIC-U0KKUZ',
                'priority' => 'Normal',
                'last_reply' => Carbon::now()->subDays(75),
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => null,
            ],
            [
                'admin_id' => getAdmin(),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'ticket_status_id' => getTicketStatusId('Open'),
                'subject' => 'New Tickets',
                'message' => 'Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.',
                'code' => 'TIC-FOPCTM',
                'priority' => 'Normal',
                'last_reply' => '2023-09-01 10:31:57',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => null,
            ],
        ]);
    }
}
