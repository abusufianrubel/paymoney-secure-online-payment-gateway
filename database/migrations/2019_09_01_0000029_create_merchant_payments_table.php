<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('merchant_payments', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('merchant_id')->constrained('merchants')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('payment_method_id')->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('gateway_reference', 50)->nullable();
            $table->string('order_no',50)->nullable();
            $table->string('item_name',150)->nullable();
            $table->string('uuid', 13)->nullable();
            $table->string('fee_bearer', 10)->comment('Merchant, User');
            $table->decimal('percentage', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('charge_percentage', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('charge_fixed', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('amount', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('total', 20, 8)->nullable()->default(0.00000000);
            $table->string('status', 11)->default('Success')->comment('Pending, Success, Refund, Blocked');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_payments');
    }
}
