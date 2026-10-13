<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Virtualcard\Entities\VirtualcardProvider;

class CreateVirtualcardFeeslimitsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_feeslimits', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(VirtualcardProvider::class)->constrained()->onUpdate('cascade')->onDelete('cascade')->onDelete('restrict');

            $table->string('virtualcard_currency_code', 3);

            $table->decimal('card_creation_fee', 20, 8)->default(0.0);

            $table->decimal('topup_percentage_fee', 20, 8)->default(0.0);
            $table->decimal('topup_fixed_fee', 20, 8)->default(0.0);
            $table->decimal('topup_min_limit', 20, 8)->nullable();
            $table->decimal('topup_max_limit', 20, 8)->nullable();

            $table->decimal('withdrawal_percentage_fee', 20, 8)->default(0.0);
            $table->decimal('withdrawal_fixed_fee', 20, 8)->default(0.0);
            $table->decimal('withdrawal_min_limit', 20, 8)->nullable();
            $table->decimal('withdrawal_max_limit', 20, 8)->nullable();
            
            $table->string('status', 11)->default('Active');

            $table->timestamps();

            $table->unique(['virtualcard_provider_id', 'virtualcard_currency_code'], 'provider_currency_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_feeslimits');
    }
}
