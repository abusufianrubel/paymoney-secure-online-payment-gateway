<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\KycVerification\Entities\KycProvider;

class KycProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name'       => 'Manual',
                'alias'      => 'manual',
                'is_default' => 'Yes',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        KycProvider::insert($providers);
    }
}
