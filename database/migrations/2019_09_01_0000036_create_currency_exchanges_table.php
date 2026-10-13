<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_exchanges', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('from_wallet')->constrained('wallets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('to_wallet')->constrained('wallets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('uuid', 13)->nullable()->comment('Unique ID (For Each Exchange)');
            $table->decimal('exchange_rate', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('amount', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('fee', 20, 8)->nullable()->default(0.00000000);//pm_v2.3
            $table->string('type', 6)->comment('In, Out');
            $table->string('status', 11)->comment('Pending, Success, Refund, Blocked');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('currency_exchanges');
    }
}
