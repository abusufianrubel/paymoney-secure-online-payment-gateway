<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Virtualcard\Entities\Virtualcard;

class CreateVirtualcardTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Virtualcard::class)
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->decimal('amount', 20, 8);
            $table->decimal('merchant_amount', 20, 8)->nullable();
            $table->string('currency', 3);
            $table->string('merchant_currency', 3)->nullable();
            $table->decimal('transaction_fees', 20, 8)->nullable();
            $table->string('card_number', 19);
            $table->string('reason', 191)->nullable();
            $table->string('transaction_id', 91);
            $table->string('status', 11);
            $table->longText('api_response')->nullable()->comment('webhook response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_transactions');
    }
}
