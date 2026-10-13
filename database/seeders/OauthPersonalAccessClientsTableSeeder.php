<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OauthPersonalAccessClientsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_personal_access_clients')->delete();
        
        DB::table('oauth_personal_access_clients')->insert([
            [
                'client_id' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'client_id' => 2,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'client_id' => 3,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
