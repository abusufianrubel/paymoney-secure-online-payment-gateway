<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('payment_methods')->delete();
        
        \DB::table('payment_methods')->insert([
            [
                'name' => 'Mts',
                'status' => 'Active',
            ],
            [
                'name' => 'Stripe',
                'status' => 'Active',
            ],
            [
                'name' => 'Paypal',
                'status' => 'Active',
            ],
            [
                'name' => 'PayUmoney',
                'status' => 'Active',
            ],
            [
                'name' => 'Bank',
                'status' => 'Active',
            ],
            [
                'name' => 'Coinpayments',
                'status' => 'Active',
            ],
            [
                'name' => 'Payeer',
                'status' => 'Active',
            ],
            [
                'name' => 'Crypto',
                'status' => 'Active',
            ],
            [
                'name' => 'Coinbase',
                'status' => 'Active',
            ],
            [
                'name' => 'Flutterwave',
                'status' => 'Active',
            ],
        ]);
    }
}
