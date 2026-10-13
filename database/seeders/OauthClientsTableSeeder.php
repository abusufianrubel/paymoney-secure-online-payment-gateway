<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class OauthClientsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->delete();
        
        DB::table('oauth_clients')->insert([
            [
                'user_id' => null,
                'name' => 'Laravel Personal Access Client',
                'secret' => 'agkL4ISxlzHE5z2zS2vwqZqqoF7ker3HMXo7De3v',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'user_id' => null,
                'name' => 'Laravel Password Grant Client',
                'secret' => 'TwF6YvwSCLuVejXhUQCAqMaPAqhHZ29sEhhFfsM9',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'updated_at' => null,
                'created_at' => null,
            ],
            [
                'user_id' => null,
                'name' => 'Laravel Personal Access Client',
                'secret' => 'YWG63Yjp0bcf7iL45MgK75Yc5Tq18KS9rcv8ltBM',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'updated_at' => null,
                'created_at' => null,
            ],
        ]);
    }
}
