<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserDetailsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_details')->delete();
        
        DB::table('user_details')->insert([
            [
                'user_id' => getUserByEmail('john@gmail.com'),
                'country_id' => getCountryId('AF'),
                'email_verification' => 1,
                'phone_verification_code' => null,
                'two_step_verification_type' => 'disabled',
                'two_step_verification_code' => null,
                'two_step_verification' => 1,
                'last_login_at' => null,
                'last_login_ip' => null,
                'city' => null,
                'state' => null,
                'address_1' => null,
                'address_2' => null,
                'default_currency' => null,
                'timezone' => 'Asia/Dhaka'
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'country_id' => getCountryId('AF'),
                'email_verification' => 1,
                'phone_verification_code' => null,
                'two_step_verification_type' => 'disabled',
                'two_step_verification_code' => null,
                'two_step_verification' => 1,
                'last_login_at' => '2023-09-04 04:32:16',
                'last_login_ip' => '::1',
                'city' => null,
                'state' => null,
                'address_1' => null,
                'address_2' => null,
                'default_currency' => null,
                'timezone' => 'Asia/Dhaka'
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'country_id' => getCountryId('AF'),
                'email_verification' => 1,
                'phone_verification_code' => null,
                'two_step_verification_type' => 'disabled',
                'two_step_verification_code' => null,
                'two_step_verification' => 1,
                'last_login_at' => null,
                'last_login_ip' => null,
                'city' => null,
                'state' => null,
                'address_1' => null,
                'address_2' => null,
                'default_currency' => null,
                'timezone' => 'Asia/Dhaka'
            ],
            [
                'user_id' => getUserByEmail('nuzhat@gmail.com'),
                'country_id' => getCountryId('AF'),
                'email_verification' => 1,
                'phone_verification_code' => null,
                'two_step_verification_type' => 'disabled',
                'two_step_verification_code' => null,
                'two_step_verification' => 1,
                'last_login_at' => null,
                'last_login_ip' => null,
                'city' => null,
                'state' => null,
                'address_1' => null,
                'address_2' => null,
                'default_currency' => null,
                'timezone' => 'Asia/Dhaka'
            ],
        ]);
    }
}
