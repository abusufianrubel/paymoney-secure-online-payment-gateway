<?php

namespace Database\Seeders;

use App\Models\Reason;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisputesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('disputes')->delete();
        
        DB::table('disputes')->insert([
            [
                'claimant_id' => getUserByEmail('kyla@gmail.com'),
                'defendant_id' => getUserByEmail('irish@gmail.com'),
                'transaction_id' => getTransactionReferenceId(Transaction::class, 'CJIGRGEWD28HB'),
                'reason_id' => Reason::first()->id,
                'title' => 'Product received isssue',
                'description' => 'consectetur adipiscing elit. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quis',
                'code' => 'DIS-WUTUZP',
                'status' => 'Solve',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now()->subDays(60),
            ],
            [
                'claimant_id' => getUserByEmail('kyla@gmail.com'),
                'defendant_id' => getUserByEmail('irish@gmail.com'),
                'transaction_id' => getTransactionReferenceId(Transaction::class, '9HNQSGQSIWL3Q'),
                'reason_id' => Reason::orderBy('id', 'desc')->first()->id,
                'title' => 'Description does not match with product',
                'description' => 'Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. lobortis in diam.',
                'code' => 'DIS-656MEZ',
                'status' => 'Closed',
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(50),
            ],
        ]);
    }
}
