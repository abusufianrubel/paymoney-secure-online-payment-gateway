<?php

namespace Modules\TatumIo\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TatumIoDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(TatumIoPaymentMethodTableSeeder::class);
        $this->call(TatumIoMetasTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(TransactionTypeTableSeeder::class);
        
        if (checkDemoEnvironment()) {
            $this->call(CryptoTokenTableSeeder::class);
            $this->call(CryptoAssetSettingsTableSeeder::class);
            $this->call(TransactionsTableSeeder::class);
            $this->call(CryptoAssetApiLogsTableSeeder::class);
        }
    }
}
