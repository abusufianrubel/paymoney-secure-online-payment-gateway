<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_limits', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('transaction_type_id')->constrained('transaction_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');

            $table->decimal('charge_percentage', 20, 8)->default(0.00000000);
            $table->decimal('charge_fixed', 20, 8)->default(0.00000000);
            $table->decimal('min_limit', 20, 8)->default(1.00000000);
            $table->decimal('max_limit', 20, 8)->nullable();
            $table->string('processing_time', 4)->nullable()->default('0')->comment('time in days'); 
            $table->string('has_transaction', 3)->comment('Yes or No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_limits');
    }
}
