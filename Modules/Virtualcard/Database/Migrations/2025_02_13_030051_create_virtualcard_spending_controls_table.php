<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualcardSpendingControlsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_spending_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtualcard_id')->constrained()->onDelete('cascade');
            $table->string('interval', 17)->comment('per_transaction, daily, weekly, monthly, yearly, all_time');
            $table->decimal('amount', 20, 8)->default(0.00);
            $table->decimal('spent', 20, 8)->nullable()->default(0.00);
            $table->json('allowed_categories')->nullable();
            $table->json('blocked_categories')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_spending_controls');
    }
}
