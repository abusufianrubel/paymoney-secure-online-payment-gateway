<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('end_user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('cascade');

            $table->string('uuid', 13)->nullable()->comment('Unique ID');

            $table->string('refund_reference', 13)->nullable()->comment('Refund Reference');

            $table->foreignId('transaction_reference_id')->default(0);
            $table->foreignId('transaction_type_id')->constrained('transaction_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('user_type', 15)->default('registered')->comment('registered, unregistered');
            $table->string('email', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('subtotal', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('percentage', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('charge_percentage', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('charge_fixed', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('total', 20, 8)->nullable()->default(0.00000000);
            $table->text('note')->nullable();
            $table->string('payment_status', 11)->comment('Pending, Success, Blocked')->nullable();
            $table->string('status', 11)->comment('Pending, Success, Refund, Blocked');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
