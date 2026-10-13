<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TransfersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('transfers')->delete();
        
        DB::table('transfers')->insert([
            [
                'sender_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => 'FJHQNHEZJSMZV',
                'fee' => '0.00000000',
                'amount' => '5.00000000',
                'note' => 'Send money for buying books.',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => null,
            ],
            [
                'sender_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('john@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => 'AOR82DDUXIDV1',
                'fee' => '0.00000000',
                'amount' => '5.00000000',
                'note' => 'just for testing',
                'email' => 'borna.techvill@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => null,
            ],
            [
                'sender_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => 'IN46V5KKDQGKG',
                'fee' => '6.00000000',
                'amount' => '50.00000000',
                'note' => 'Quisque congue porttitor placerat.  Quisque at ut mi porttitor eleifend nec nec erat.',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => null,
            ],
            [
                'sender_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('john@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => '3L2HS0QZUBO6W',
                'fee' => '0.00000000',
                'amount' => '10.00000000',
                'note' => 'Quisque congue porttitor placerat.  Quisque at ut mi porttitor eleifend nec nec erat.',
                'email' => 'borna.techvill@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => null,
                
            ],
            [
                'sender_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => 'UTGCIVW52KHWA',
                'fee' => '0.00000000',
                'amount' => '10.00000000',
                'note' => 'Quisque congue porttitor placerat. ',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => null,
            ],
            [
                'sender_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => 'TMUA9QTPCVSLR',
                'fee' => '6.44000000',
                'amount' => '41.00000000',
                'note' => 'demo send money',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => null,
            ],
            [
                'sender_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'bank_id' => null,
                'file_id' => null,
                'uuid' => '1DE5C0C36F389',
                'fee' => '5.60000000',
                'amount' => '200.00000000',
                'note' => 'This is demo note for sending money',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
        ]);
    }
}
