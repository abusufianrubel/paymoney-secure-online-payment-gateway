<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParametersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('parameters')->delete();
    }
}
