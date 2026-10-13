<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        
        DB::table('users')->insert([
            [
                'role_id' => getUserRoleID('user'),
                'type' => 'user',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'formattedPhone' => null,
                'phone' => null,
                'google2fa_secret' => null,
                'defaultCountry' => null,
                'carrierCode' => null,
                'email' => 'john@gmail.com',
                'password' => '$2y$10$tOZgPWcVHFdbg3CXOtgryOm0iG6GAWA9mUjpWTmyTajN3a3o/.FDy',
                'phrase' => null,
                'address_verified' => 0,
                'identity_verified' => 0,
                'status' => 'Inactive',
                'remember_token' => 'YjD0qcVZRrzlfpuayTIxXj1JRh3R79x5VVZy5tw9aSZzvDbRHCjaqPGKK2fN',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
                'picture' => '1532328230.jpg',
            ],
            [
                'role_id' => getUserRoleID('merchant'),
                'type' => 'merchant',
                'first_name' => 'Irish',
                'last_name' => 'Watson',
                'formattedPhone' => null,
                'phone' => null,
                'google2fa_secret' => null,
                'defaultCountry' => null,
                'carrierCode' => null,
                'email' => 'irish@gmail.com',
                'password' => '$2y$10$Ok2hFLTlMsxrW.Ho0/bzMuZzdabJ.xfqimk8Iy.8zqKCnxFO9Q/ji',
                'phrase' => null,
                'address_verified' => 1,
                'identity_verified' => 1,
                'status' => 'Active',
                'remember_token' => '10e5nYjqCFDZpohF9g3kRo7D2Mj3wQ5BygIMvtjTDxDXfkN160YffTzxA84k',
                'created_at' => Carbon::now()->subDays(771),
                'updated_at' => Carbon::now()->subDays(771),
                'picture' => '1532005837.jpg',
            ],
            [
                'role_id' => getUserRoleID('user'),
                'type' => 'user',
                'first_name' => 'Kyla',
                'last_name' => 'Sarah',
                'formattedPhone' => null,
                'phone' => null,
                'google2fa_secret' => null,
                'defaultCountry' => null,
                'carrierCode' => null,
                'email' => 'kyla@gmail.com',
                'password' => '$2y$10$5ey1WW9lvWuEgNHs0Ofgr.lZYzdnnMBnTqHQ5E9dZ6FKcsG1A.eIi',
                'phrase' => null,
                'address_verified' => 0,
                'identity_verified' => 0,
                'status' => 'Active',
                'remember_token' => 'enqK5X7BQ8Fydoa0BN7kdddcHktLwYNmyBTxkwrYzlQASYcyvcGlrN45ReaS',
                'created_at' => Carbon::now()->subDays(772),
                'updated_at' => Carbon::now()->subDays(772),
                'picture' => '1532333460.png',
            ],
            [
                'role_id' => getUserRoleID('user'),
                'type' => 'user',
                'first_name' => 'Nishat',
                'last_name' => 'Nuzhat',
                'formattedPhone' => null,
                'phone' => null,
                'google2fa_secret' => null,
                'defaultCountry' => null,
                'carrierCode' => null,
                'email' => 'nuzhat@gmail.com',
                'password' => '$2y$10$bcUIXCZexgN/tBtQejsZCeky8WvM.7V/V5DlGPzmB/C9Fnh0U3TXy',
                'phrase' => null,
                'address_verified' => 0,
                'identity_verified' => 0,
                'status' => 'Suspended',
                'remember_token' => 'WgOH279USfF4S36SXaCMY6JXYAw0GbFsjODQw7fXRLi0O9rAlZBcccP2yCXr',
                'created_at' => Carbon::now()->subDays(600),
                'updated_at' => Carbon::now()->subDays(600),
                'picture' => '1532342904.jpg',
            ],
        ]);
    }
}
