<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('txid', 100);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('network', 100);
            $table->string('symbol', 10);
            $table->string('address', 100)->nullable();
            $table->string('decimals', 10);
            $table->string('value', 100);
            $table->string('status', 11)->default('Active');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_tokens');
    }
};
