<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        
        DB::table('admins')->insert([
            [
                'role_id' => \App\Models\Role::first()->id,
                'first_name' => 'Admin',
                'last_name' => 'Techvill',
                'phone' => null,
                'email' => 'admin@techvill.net',
                'password' => '$2y$10$1kiQOm3BB3JBSWHonTliNeAHaEZEDgawpz6Cd1ty8ybG37aKIpsza',
                'status' => 'Active',
                'remember_token' => 'UsntIYqnFdcxpIJkjV4lcJpUnP3oVBNKLmEK7wLo3yjXeg4lFOOLDUCfyPo2',
                'created_at' => Carbon::now()->subDays(800),
                'updated_at' => Carbon::now()->subDays(800),
                'picture' => '273.jpg',
            ],
        ]);
        
        
    }
}
