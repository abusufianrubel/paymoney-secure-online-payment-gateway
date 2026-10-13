<?php

namespace Modules\TatumIo\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CryptoTokenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('crypto_tokens')->delete();

        DB::table('crypto_tokens')->insert([
            [
                'txid' => '66611db630c9c641ad193f67533939d7ff5fad18538d03ce3f',
                'currency_id' => getUSDTTokenId(),
                'name' => 'Tether Token',
                'network' => 'TRXTEST',
                'symbol' => 'USDT',
                'address' => 'TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs',
                'decimals' => 6,
                'value' => 100.1148,
                'status' => 'Active',
                'created_at' => null,
                'updated_at' => null,
            ]
        ]);
    }
}
