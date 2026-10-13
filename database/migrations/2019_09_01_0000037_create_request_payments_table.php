<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_payments', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('uuid', 13)->nullable()->comment('Unique ID (For Each Payment Request)');
            $table->decimal('amount', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('accept_amount', 20, 8)->nullable()->default(0.00000000);
            $table->string('email', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('purpose')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 11)->comment('Pending, Success, Refund, Blocked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('request_payments');
    }
}
