<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('transaction_types')->delete();

        \DB::table('transaction_types')->insert([
            [
                'name' => 'Deposit',
            ],
            [
                'name' => 'Withdrawal',
            ],
            [
                'name' => 'Transferred',
            ],
            [
                'name' => 'Received',
            ],
            [
                'name' => 'Exchange_From',
            ],
            [
                'name' => 'Exchange_To',
            ],
            [
                'name' => 'Request_Sent',
            ],
            [
                'name' => 'Request_Received',
            ],
            [
                'name' => 'Payment_Sent',
            ],
            [
                'name' => 'Payment_Received',
            ]
        ]);
    }
}
