<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TicketStatusesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('ticket_statuses')->delete();
        
        DB::table('ticket_statuses')->insert([
            [
                'name' => 'Open',
                'color' => '00a65a',
                'is_default' => 0,
            ],
            [
                'name' => 'In Progress',
                'color' => '3c8dbc',
                'is_default' => 1,
            ],
            [
                'name' => 'Hold',
                'color' => 'f39c12',
                'is_default' => 0,
            ],
            [
                'name' => 'Closed',
                'color' => 'dd4b39',
                'is_default' => 0,
            ],
        ]);
    }
}
