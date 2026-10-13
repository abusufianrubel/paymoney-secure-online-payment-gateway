<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PreferencesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('preferences')->delete();

        DB::table('preferences')->insert([
            [
                'category' => 'preference',
                'field' => 'row_per_page',
                'value' => '25',
            ],
            [
                'category' => 'preference',
                'field' => 'date_format',
                'value' => '1',
            ],
            [
                'category' => 'preference',
                'field' => 'date_sepa',
                'value' => '-',
            ],
            [
                'category' => 'company',
                'field' => 'site_short_name',
                'value' => 'PM',
            ],
            [
                'category' => 'preference',
                'field' => 'percentage',
                'value' => '2',
            ],
            [
                'category' => 'preference',
                'field' => 'quantity',
                'value' => '0',
            ],
            [
                'category' => 'preference',
                'field' => 'date_format_type',
                'value' => 'dd-mm-yyyy',
            ],
            [
                'category' => 'company',
                'field' => 'company_name',
                'value' => 'Pay Money',
            ],
            [
                'category' => 'company',
                'field' => 'company_email',
                'value' => 'admin@techvill.net',
            ],
            [
                'category' => 'company',
                'field' => 'dflt_lang',
                'value' => 'en',
            ],
            [
                'category' => 'preference',
                'field' => 'default_money_format',
                'value' => '&nbsp;&#36;',
            ],
            [
                'category' => 'preference',
                'field' => 'money_format',
                'value' => 'before',
            ],
            [
                'category' => 'preference',
                'field' => 'decimal_format_amount',
                'value' => '2',
            ],
            [
                'category' => 'preference',
                'field' => 'thousand_separator',
                'value' => ',',
            ],
            [
                'category' => 'preference',
                'field' => 'dflt_timezone',
                'value' => 'Asia/Dhaka',
            ],
            [
                'category' => 'preference',
                'field' => 'verification_mail',
                'value' => 'Disabled',
            ],
            [
                'category' => 'preference',
                'field' => 'phone_verification',
                'value' => 'Disabled',
            ],
            [
                'category' => 'preference',
                'field' => 'two_step_verification',
                'value' => 'disabled',
            ],
            [
                'category' => 'preference',
                'field' => 'processed_by',
                'value' => 'email',
            ],
            [
                'category' => 'preference',
                'field' => 'decimal_format_amount_crypto',
                'value' => '8',
            ],
            [
                'category' => 'preference',
                'field' => 'admin_url_prefix',
                'value' => 'admin',
            ],
            [
                'category' => 'preference',
                'field' => 'file_size',
                'value' => '2',
            ],
        ]);
    }
}
