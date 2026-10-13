<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MerchantGroupsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchant_groups')->delete();
        
        DB::table('merchant_groups')->insert([
            [
                'name' => 'Premium',
                'description' => 'This is the premium merchant group',
                'fee' => '0.50000000',
                'fee_bearer' => 'Merchant',
                'is_default' => 'No',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'name' => 'Gold',
                'description' => 'This is the gold merchant group',
                'fee' => '1.00000000',
                'fee_bearer' => 'Merchant',
                'is_default' => 'No',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'name' => 'Silver',
                'description' => 'This is the silver merchant group',
                'fee' => '1.50000000',
                'fee_bearer' => 'Merchant',
                'is_default' => 'Yes',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'name' => 'Bronze',
                'description' => 'This is the bronze merchant group',
                'fee' => '2.00000000',
                'fee_bearer' => 'Merchant',
                'is_default' => 'No',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
