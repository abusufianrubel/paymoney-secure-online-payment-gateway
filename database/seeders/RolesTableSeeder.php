<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();
        
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'display_name' => 'Admin',
                'description' => 'Admin',
                'user_type' => 'Admin',
                'customer_type' => 'user',
                'is_default' => 'No',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'name' => 'Default User',
                'display_name' => 'Default User',
                'description' => 'Default User',
                'user_type' => 'User',
                'customer_type' => 'user',
                'is_default' => 'Yes',
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'name' => 'Merchant Regular',
                'display_name' => 'Merchant Regular',
                'description' => 'Merchant Regular',
                'user_type' => 'User',
                'customer_type' => 'merchant',
                'is_default' => 'Yes',
                'created_at' => null,
                'updated_at' => null,
           ],
        ]);
    }
}
