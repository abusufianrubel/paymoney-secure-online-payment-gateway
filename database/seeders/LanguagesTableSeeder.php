<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->delete();
        
        DB::table('languages')->insert([
            [
                'name' => 'English',
                'short_name' => 'en',
                'flag' => '1530358989.png',
                'default' => '1',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'عربى',
                'short_name' => 'ar',
                'flag' => '1530359409.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Français',
                'short_name' => 'fr',
                'flag' => '1530359431.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Português',
                'short_name' => 'pt',
                'flag' => '1530359450.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Русский',
                'short_name' => 'ru',
                'flag' => '1530359474.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Español',
                'short_name' => 'es',
                'flag' => '1530360151.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => 'Türkçe',
                'short_name' => 'tr',
                'flag' => '1530696845.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
            [
                'name' => '中文 (繁體)',
                'short_name' => 'ch',
                'flag' => '1531227913.png',
                'default' => '0',
                'deletable' => 'No',
                'status' => 'Active',
            ],
        ]);
    }
}
