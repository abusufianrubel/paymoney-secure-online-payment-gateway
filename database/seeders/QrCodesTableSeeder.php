<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QrCodesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('qr_codes')->delete();
        
        DB::table('qr_codes')->insert([
            [
                'object_id' => getUserByEmail('irish@gmail.com'),
                'object_type' => 'user',
                'secret' => 'eS9nOG54dHRmejFpeW0wU3Q4Y0owQ3docE0vMW1FZ3d2VllZN05UcFBKdz0=',
                'qr_image' => null,
                'status' => 'Inactive',
                'created_at' => Carbon::now()->subDays(771),
                'updated_at' => Carbon::now()->subDays(771),
            ],
            [
                'object_id' => getUserByEmail('irish@gmail.com'),
                'object_type' => 'user',
                'secret' => 'eS9nOG54dHRmejFpeW0wU3Q4Y0owR3hiZkpNU1N6ZFBNeDIyTE1vdktlMD0=',
                'qr_image' => '1688455489.jpg',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
            ],
            [
                'object_id' => getUserByEmail('kyla@gmail.com'),
                'object_type' => 'user',
                'secret' => 'WlRGRTZadVU5VDVaUTNFQ0NxRTB1bW9Qc1JwaWFDUDRXLzZmREZISmRNcz0=',
                'qr_image' => '1688456575.jpg',
                'status' => 'Active',
                'created_at' => Carbon::now()->subDays(772),
                'updated_at' => Carbon::now()->subDays(772),
            ]
        ]);
    }
}
