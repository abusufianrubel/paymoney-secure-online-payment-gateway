<?php

namespace Modules\TatumIo\Database\Seeders\versions\v4_3_0;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TatumIoMetasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $metas = [
            ['url' => 'token/send/tatumio/{walletCurrencyCode}/{walletId}', 'title' => 'Token Send', 'description' => 'Token Send', 'keywords' => ''],
            ['url' => 'token/send/tatumio/confirm', 'title' => 'Send Token Confirm', 'description' => 'Send Token Confirm', 'keywords' => ''],
            ['url' => 'token/send/tatumio/success', 'title' => 'Send Token Success', 'description' => 'Send Token Success', 'keywords' => ''],
            ['url' => 'token/receive/tatumio/{walletCurrencyCode}/{walletId}', 'title' => 'Token Receive', 'description' => 'Token Receive', 'keywords' => ''],
        ];

        \App\Models\Meta::insert($metas);
    }
}
