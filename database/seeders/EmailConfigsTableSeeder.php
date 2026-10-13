<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EmailConfigsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_configs')->delete();

        if (checkDemoEnvironment()) {
            DB::table('email_configs')->insert([
                [
                    'email_protocol' => 'smtp',
                    'email_encryption' => 'tls',
                    'smtp_host' => 'mail.techvill.org',
                    'smtp_port' => '2525',
                    'from_address' => 'helptechvillage@techvill.org',
                    'smtp_email' => 'helptechvillage@techvill.org',
                    'smtp_password' => '!]sxjW9a$wCi',
                    'smtp_username' => 'helptechvillage@techvill.org',
                    'from_name' => 'Pay Money',
                    'status' => 1,
                ]
            ]);
        } else {
            DB::table('email_configs')->insert([
                [
                    'email_protocol' => 'sendmail',
                    'email_encryption' => 'tls',
                    'smtp_host' => '',
                    'smtp_port' => '587',
                    'from_address' => '',
                    'smtp_email' => '',
                    'smtp_password' => '',
                    'smtp_username' => '',
                    'from_name' => '',
                    'status' => 1,
                ]
            ]);
        }
    }
}
