<?php

namespace Modules\KycVerification\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentVerificationsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        if (checkDemoEnvironment()) {
            DB::table('document_verifications')->insert([
                [
                    'provider_id' => getKycProviderId('manual'),
                    'user_id' => getUserByEmail('irish@gmail.com'),
                    'file_id' => getFileId('1688456042.png'),
                    'verification_type' => 'identity',
                    'identity_type' => 'passport',
                    'identity_number' => '1520004698',
                    'status' => 'approved',
                    'created_at' => Carbon::now()->subDays(200),
                    'updated_at' => Carbon::now()->subDays(200),
                ],
                [
                    'provider_id' => getKycProviderId('manual'),
                    'user_id' => getUserByEmail('irish@gmail.com'),
                    'file_id' => getFileId('1688456225.png'),
                    'verification_type' => 'address',
                    'identity_number' => null,
                    'identity_type' => null,
                    'status' => 'approved',
                    'created_at' => Carbon::now()->subDays(180),
                    'updated_at' => Carbon::now()->subDays(180),
                ]
            ]);
        }
    }
}
