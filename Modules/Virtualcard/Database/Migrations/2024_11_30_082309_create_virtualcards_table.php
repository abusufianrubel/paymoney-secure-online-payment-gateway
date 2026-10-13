<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualcardsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('virtualcard_provider_id')->nullable()->constrained('virtualcard_providers');
            $table->foreignId('virtualcard_category_id')->constrained('virtualcard_categories');
            $table->foreignId('virtualcard_holder_id')->constrained('virtualcard_holders');

            $table->string('card_type', 20)->default('Virtual');
            $table->string('card_brand', 30)->index('virtualcards_card_brand_idx')->comment('Visa, Mastercard');
            $table->string('card_number', 19);
            $table->string('card_cvc', 4)->nullable();
            $table->string('currency_code', 3)->index('virtualcards_currency_code_idx');
            $table->decimal('amount', 20, 8)->nullable()->default(0);
            $table->integer('expiry_month')->nullable();
            $table->integer('expiry_year')->nullable();

            $table->string('status', 11)->default('Pending')->comment('Active, Inactive, Pending, Freeze, Terminate, Expired');

            $table->string('api_card_id', 50)->nullable()->comment('Provider card id');
            $table->longText('api_card_response')->nullable()->comment('Provider card response');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcards');
    }
}
