<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CurrencyPaymentMethodsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency_payment_methods')->delete();

        $now = now();

        DB::table('currency_payment_methods')->insert([
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Stripe'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Paypal'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'client_id' => '',
                    'client_secret' => '',
                    'mode' => 'sandbox',
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('EUR'),
                'method_id' => getPaymentMethodId('Paypal'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'client_id' => '',
                    'client_secret' => '',
                    'mode' => 'sandbox',
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('GBP'),
                'method_id' => getPaymentMethodId('Stripe'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('GBP'),
                'method_id' => getPaymentMethodId('Paypal'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'client_id' => '',
                    'client_secret' => '',
                    'mode' => 'sandbox',
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('EUR'),
                'method_id' => getPaymentMethodId('Stripe'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'secret_key' => env('STRIPE_SECRET_KEY', ''),
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
                ]),
                'processing_time' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Bank'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode(['bank_id' => 1]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Bank'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode(['bank_id' => 2]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Bank'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode(['bank_id' => 3]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('GBP'),
                'method_id' => getPaymentMethodId('Bank'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode(['bank_id' => 4]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('EUR'),
                'method_id' => getPaymentMethodId('Bank'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode(['bank_id' => 5]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'currency_id' => getCurrencyId('USD'),
                'method_id' => getPaymentMethodId('Flutterwave'),
                'activated_for' => json_encode(['deposit' => '']),
                'method_data' => json_encode([
                    'public_key' => env('FLUTTERWAVE_PUBLIC_KEY', ''),
                    'secret_key' => env('FLUTTERWAVE_SECRET_KEY', ''),
                    'secret_hash' => env('FLUTTERWAVE_SECRET_HASH', ''),
                ]),
                'processing_time' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
