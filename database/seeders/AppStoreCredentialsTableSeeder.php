<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AppStoreCredentialsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_store_credentials')->delete();
        
        DB::table('app_store_credentials')->insert([
            [
                'has_app_credentials' => 'Yes',
                'link' => 'http://store.google.com/pay-money',
                'logo' => '1531650482.png',
                'company' => 'Google',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'has_app_credentials' => 'Yes',
                'link' => 'https://itunes.apple.com/bd/app/pay-money',
                'logo' => '1531134592.png',
                'company' => 'Apple',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
