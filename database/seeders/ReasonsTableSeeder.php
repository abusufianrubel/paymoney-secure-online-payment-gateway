<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('reasons')->delete();
        
        DB::table('reasons')->insert([
            [
                'title' => 'I have not received the goods',
            ],
            [
                'title' => 'Description does not match with product',
            ],
        ]);
    }
}
