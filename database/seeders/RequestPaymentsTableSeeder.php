<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RequestPaymentsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('request_payments')->delete();
        
        DB::table('request_payments')->insert([
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'uuid' => 'IHOUJP6IZQ1X2',
                'amount' => '100.00000000',
                'accept_amount' => '13.00000000',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'Please give the amount',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(85),
                'updated_at' => Carbon::now()->subDays(85),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'uuid' => 'IXOP794RXPX0D',
                'amount' => '50.00000000',
                'accept_amount' => '2.00000000',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'please pay 50 usd',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(78),
                'updated_at' => Carbon::now()->subDays(78),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => 'RPEBW8C2Q2CUY',
                'amount' => '5.00000000',
                'accept_amount' => '5.00000000',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie , nec placerat nibh. Donec placerat interdum libero eu blandit. Quisque at ut mi porttitor eleifend nec nec erat.',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(60),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => '3VB90T1JHXRAM',
                'amount' => '2.00000000',
                'accept_amount' => '0.00000000',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat.',
                'status' => 'Blocked',
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(50),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'uuid' => 'KSYU0HEO0TX6W',
                'amount' => '50.00000000',
                'accept_amount' => '2.00000000',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'please pay 50 usd',
                'status' => 'Refund',
                'created_at' => Carbon::now()->subDays(65),
                'updated_at' => Carbon::now()->subDays(65),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('nuzhat@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'uuid' => 'M0FEDRDYGVXVM',
                'amount' => '3.00000000',
                'accept_amount' => '0.00000000',
                'email' => 'mahfuzasinthy@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat.',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(40),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('GBP'),
                'uuid' => '5AQROW2KBH7EB',
                'amount' => '43.00000000',
                'accept_amount' => '0.00000000',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'demo request money',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'user_id' => getUserByEmail('irish@gmail.com'),
                'receiver_id' => getUserByEmail('kyla@gmail.com'),
                'currency_id' => getCurrencyId('EUR'),
                'uuid' => 'E1ADA596F8EBE',
                'amount' => '50.49000000',
                'accept_amount' => '0.00000000',
                'email' => 'kyla@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'Please provide me some EUR',
                'status' => 'Pending',
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(45),
            ],
            [
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'receiver_id' => getUserByEmail('irish@gmail.com'),
                'currency_id' => getCurrencyId('USD'),
                'uuid' => 'D482FF478E0CC',
                'amount' => '10.00000000',
                'accept_amount' => '10.00000000',
                'email' => 'irish@gmail.com',
                'phone' => null,
                'purpose' => null,
                'note' => 'This is a test request',
                'status' => 'Success',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
        ]);
    }
}
