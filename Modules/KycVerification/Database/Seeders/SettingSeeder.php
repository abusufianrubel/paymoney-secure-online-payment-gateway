<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['name' => 'kyc_provider', 'value' => 'manual', 'type' => 'kyc_verification'],
            ['name' => 'kyc_mandatory', 'value' => 'No', 'type' => 'kyc_verification'],
            ['name' => 'kyc_required_for', 'value' => 'All', 'type' => 'kyc_verification'],
        ];

        \App\Models\Setting::insert($settings);
    }
}
