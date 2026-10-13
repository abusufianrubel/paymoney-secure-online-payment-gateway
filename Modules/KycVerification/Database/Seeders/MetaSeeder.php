<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;

class MetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metas = [
            ['url' => 'user/kyc/verifications', 'title' => 'Kyc Verification', 'description' => 'Kyc Verification', 'keywords' => ''],
            ['url' => 'user/kyc/address-verifications', 'title' => 'Kyc Address Verification', 'description' => 'Kyc Address Verification', 'keywords' => ''],
            ['url' => 'user/kyc/proof-download/{fileName}', 'title' => 'Verification Proof Download', 'description' => 'Verification Proof Download', 'keywords' => '']
        ];

        \App\Models\Meta::insert($metas);
    }
}
