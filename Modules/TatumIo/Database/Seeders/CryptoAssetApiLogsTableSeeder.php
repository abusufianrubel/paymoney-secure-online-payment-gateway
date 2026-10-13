<?php

namespace Modules\TatumIo\Database\Seeders;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CryptoAssetApiLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crypto_asset_api_logs')->delete();

        DB::table('crypto_asset_api_logs')->insert([
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('LTCTEST')),
                'object_type' => 'wallet_address',
                'network' => 'LTCTEST',
                'payload' => '{"address":"msMkTifDacNY4sACdyRAUtuV7Ck67pF9sT","key":"cNNP8JBybxXRjmr5EWZaZaw23J1HRnpL8iDA877WtTFieVXUDjoy","balance":0,"user_id":1,"wallet_id":5,"network":"LTCTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(150),
                'updated_at' => Carbon::now()->subDays(150),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('BTCTEST')),
                'object_type' => 'wallet_address',
                'network' => 'BTCTEST',
                'payload' => '{"address":"tb1q6schysyzhs4w4gau6c44fq63jz7d0tla25lgrd","key":"cUcBa5bjBSiV5aFw6zpTyZVMg1PQUHy56BDiP2RNX5j3y5ywUWeR","balance":0,"user_id":1,"wallet_id":4,"network":"BTCTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(145),
                'updated_at' => Carbon::now()->subDays(145),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('LTCTEST')),
                'object_type' => 'wallet_address',
                'network' => 'LTCTEST',
                'payload' => '{"address":"mprBDah9SLXH7BQfJ2Sr9coFncbs5FtNGJ","key":"cUZquM9dceFntQJDuecvvxgtsjhVERpND5ZBwJpDhKwFBDNkuGF3","balance":0,"user_id":2,"wallet_id":12,"network":"LTCTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(140),
                'updated_at' => Carbon::now()->subDays(140),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('BTCTEST')),
                'object_type' => 'wallet_address',
                'network' => 'BTCTEST',
                'payload' => '{"address":"tb1qcamnf66jwmj4gwxqu6k63w9eak6zkd88l32cvh","key":"cVkKX3D6VJRDRQVoju3qUtXBnoKub2bvqJJJ8Kwvx9DJMXLyt7aX","balance":0,"user_id":2,"wallet_id":11,"network":"BTCTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(135),
                'updated_at' => Carbon::now()->subDays(135),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, '37ED90852E4D5'),
                'object_type' => 'crypto_sent',
                'network' => 'LTCTEST',
                'payload' => '{"network":"LTCTEST","network_fee":"0.00006740","blockio_fee":"0.00000000","total_amount_to_send":"0.00200000","txid":"751ccc1e6186a3efaa263f5eccf50309aed01d9810aadaf2225c505b83bf2a71","senderAddress":"myux6MNaBVnJkxz8Z89hmfKdci8cB4avrH","receiverAddress":"mwSHt9jDZ52XFXQTbhK6KXyBYc1Qmqx1Ae"}',
                'confirmations' => 5,
                'created_at' => Carbon::now()->subDays(130),
                'updated_at' => Carbon::now()->subDays(130),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, 'A95EC0159E9BF'),
                'object_type' => 'crypto_received',
                'network' => 'LTCTEST',
                'payload' => '{"network":"LTCTEST","address":"mwSHt9jDZ52XFXQTbhK6KXyBYc1Qmqx1Ae","balance_change":"0.00200000","amount_sent":"0.00000000","amount_received":"0.00200000","txid":"751ccc1e6186a3efaa263f5eccf50309aed01d9810aadaf2225c505b83bf2a71","confirmations":"1","is_green":null,"senderAddress":"myux6MNaBVnJkxz8Z89hmfKdci8cB4avrH","receiverAddress":"mwSHt9jDZ52XFXQTbhK6KXyBYc1Qmqx1Ae"}',
                'confirmations' => 5,
                'created_at' => Carbon::now()->subDays(125),
                'updated_at' => Carbon::now()->subDays(125),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('DOGETEST')),
                'object_type' => 'wallet_address',
                'network' => 'DOGETEST',
                'payload' => '{"address":"niUHohaJtU1192Q57wBAF6rqKAanN6PDDx","key":"cf14d81x2m5drxYQbnoaPhqTboJUTKXb76x28U4K1PfTHGqeA8e1","balance":0,"user_id":1,"wallet_id":6,"network":"DOGETEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(120),
                'updated_at' => Carbon::now()->subDays(120),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('DOGETEST')),
                'object_type' => 'wallet_address',
                'network' => 'DOGETEST',
                'payload' => '{"address":"nq41j71NeGTTMY2kHky9uHDTUtpoXCpyDA","key":"cjG7a7apqKdBzYqCpRW8h6XmzKoiHfk3deqZ5qtVuWfxgRq4PwDX","balance":0,"user_id":2,"wallet_id":13,"network":"DOGETEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(115),
                'updated_at' => Carbon::now()->subDays(115),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, '0EF56684414D7'),
                'object_type' => 'crypto_received',
                'network' => 'DOGETEST',
                'payload' => '{"txId":"ee96656c6fc6e2a4586aedbb9b57875503c1cb374a66182364111a59888e70d4","receiverAddress":"njsBLaRgk66e1yzqdvAK4SFNapuLfMZugE","senderAddress":"nXZDuf8ooThZKFDAC1ExwT6nJK7jWSWPRc"}',
                'confirmations' => 7,
                'created_at' => Carbon::now()->subDays(110),
                'updated_at' => Carbon::now()->subDays(110),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, 'C346154FE1948'),
                'object_type' => 'crypto_sent',
                'network' => 'DOGETEST',
                'payload' => '{"txId":"76e6ac8bbcfbde8b028db1891100fdc39f0aebf9007e3350421a9fe0a2ad5bc9","network_fee":"0.25807418","senderAddress":"njsBLaRgk66e1yzqdvAK4SFNapuLfMZugE","receiverAddress":"nXZDuf8ooThZKFDAC1ExwT6nJK7jWSWPRc"}',
                'confirmations' => 7,
                'created_at' => Carbon::now()->subDays(105),
                'updated_at' => Carbon::now()->subDays(105),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, 'C346154FE1948') + 1, // crypto sent transaction has same uuid so crypto received transaction will be crypto sent + 1
                'object_type' => 'crypto_received',
                'network' => 'DOGETEST',
                'payload' => '{"txId":"76e6ac8bbcfbde8b028db1891100fdc39f0aebf9007e3350421a9fe0a2ad5bc9","senderAddress":"njsBLaRgk66e1yzqdvAK4SFNapuLfMZugE","receiverAddress":"nXZDuf8ooThZKFDAC1ExwT6nJK7jWSWPRc"}',
                'confirmations' => 7,
                'created_at' => Carbon::now()->subDays(95),
                'updated_at' => Carbon::now()->subDays(95),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('irish@gmail.com'), getCurrencyId('TRXTEST')),
                'object_type' => 'wallet_address',
                'network' => 'TRXTEST',
                'payload' => '{"address":"TK61u21SARZUFAzfHQabYzefJLVBapCvad","key":"0e2be95e0ad5810e9df71a921734e2d2747804e3f9fa856ed9b1cdfa1833a918","balance":0,"user_id":1,"wallet_id":7,"network":"TRXTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => Carbon::now()->subDays(90),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getWalletId(getUserByEmail('kyla@gmail.com'), getCurrencyId('TRXTEST')),
                'object_type' => 'wallet_address',
                'network' => 'TRXTEST',
                'payload' => '{"address":"TGkwxyPD2vvsmG6L6yvt8PJ3NME5kcbcT1","key":"57a01a48a81ec2c48989fc1dfbfb674a11833f3a5f86aab4a88a38abcff66c7c","balance":0,"user_id":2,"wallet_id":14,"network":"TRXTEST"}',
                'confirmations' => 0,
                'created_at' => Carbon::now()->subDays(85),
                'updated_at' => Carbon::now()->subDays(85),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, '295F40B9EA335'),
                'object_type' => 'token_sent',
                'network' => 'USDT',
                'payload' => '{"txId":"2458094f8eba532e68a3b543b216376bc4d2e6083a923b68b531e1fc9525b1dd","network_fee":"5.82348","senderAddress":"TGsQV29rX8GjUYi1E8uq7MEAvaggJaEJVS","receiverAddress":"TChh4sFyYERb45e2DsWQ5PhhBzvYSdRxFu","token":"USDT","network":"TRXTEST"}',
                'confirmations' => 7,
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => Carbon::now()->subDays(80),
            ],
            [
                'payment_method_id' => getPaymentMethodId('TatumIo'),
                'object_id' => getTransactionReferenceId(Transaction::class, '295F40B9EA335') + 1, // token sent transaction has same uuid so token received transaction will be token sent + 1
                'object_type' => 'token_received',
                'network' => 'USDT',
                'payload' => '{"txId":"2458094f8eba532e68a3b543b216376bc4d2e6083a923b68b531e1fc9525b1dd","senderAddress":"TGsQV29rX8GjUYi1E8uq7MEAvaggJaEJVS","receiverAddress":"TChh4sFyYERb45e2DsWQ5PhhBzvYSdRxFu","network":"TRXTEST" }',
                'confirmations' => 7,
                'created_at' => Carbon::now()->subDays(75),
                'updated_at' => Carbon::now()->subDays(75),
            ]
        ]);
    }
}
