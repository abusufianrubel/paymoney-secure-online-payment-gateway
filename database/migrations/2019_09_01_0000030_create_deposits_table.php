<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->string('uuid', 13)->nullable()->comment('Unique ID (For Each Deposit)');
            $table->decimal('charge_percentage', 20,8)->nullable()->default(0.00000000);
            $table->decimal('charge_fixed', 20,8)->nullable()->default(0.00000000);
            $table->decimal('amount', 20,8)->nullable()->default(0.00000000);
            $table->string('status', 11)->comment('Pending, Success, Refund, Blocked');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
