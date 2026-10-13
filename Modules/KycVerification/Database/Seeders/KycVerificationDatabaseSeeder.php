<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;

class KycVerificationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            MetaSeeder::class,
            SettingSeeder::class,
            PermissionSeeder::class,
            KycProviderSeeder::class,
            PermissionRoleSeeder::class,
            FileSeeder::class,
            DocumentVerificationsTableSeeder::class,
            EmailTemplatesTableSeeder::class
        ]);
    }
}
