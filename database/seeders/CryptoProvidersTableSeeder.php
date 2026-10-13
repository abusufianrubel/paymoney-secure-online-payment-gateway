<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CryptoProvidersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('crypto_providers')->delete();
        
        \DB::table('crypto_providers')->insert([
            [
                'name' => 'TatumIo',
                'alias' => strtolower('TatumIo'),
                'description' => 'Tatum offers a flexible framework to build, run, and scale blockchain apps fast.',
                'logo' => null,
                'subscription_details' => '',
                'status' => 'Active',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
