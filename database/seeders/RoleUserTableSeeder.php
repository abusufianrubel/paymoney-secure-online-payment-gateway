<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user')->delete();

        if (checkDemoEnvironment()) {
            DB::table('role_user')->insert([
                [
                    'user_id' => getAdmin(),
                    'role_id' => \App\Models\Role::where('user_type', 'Admin')->first()->id,
                    'user_type' => 'Admin',
                ],
                [
                    'user_id' => getUserByEmail('john@gmail.com'),
                    'role_id' => getUserRoleID('user'),
                    'user_type' => 'User',
                ],
                [
                    'user_id' => getUserByEmail('irish@gmail.com'),
                    'role_id' => getUserRoleID('merchant'),
                    'user_type' => 'User',
                ],
                [
                    'user_id' => getUserByEmail('kyla@gmail.com'),
                    'role_id' => getUserRoleID('user'),
                    'user_type' => 'User',
                ],
                [
                    'user_id' => getUserByEmail('nuzhat@gmail.com'),
                    'role_id' => getUserRoleID('user'),
                    'user_type' => 'User',
                ]
            ]);
        }

        if (app()->runningInConsole() && !checkDemoEnvironment()) {
            DB::table('role_user')->insert([
                [
                    'user_id' => getAdmin(),
                    'role_id' => \App\Models\Role::where('user_type', 'Admin')->first()->id,
                    'user_type' => 'Admin',
                ],
            ]);
        }
    }
}
