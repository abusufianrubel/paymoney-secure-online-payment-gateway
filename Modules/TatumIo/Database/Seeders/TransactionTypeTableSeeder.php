<?php

namespace Modules\TatumIo\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TransactionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \App\Models\TransactionType::insert([
            ['name' => 'Crypto_Sent'],
            ['name' => 'Crypto_Received'],
            ['name' => 'Token_Sent'],
            ['name' => 'Token_Received'],
        ]);
    }
}
