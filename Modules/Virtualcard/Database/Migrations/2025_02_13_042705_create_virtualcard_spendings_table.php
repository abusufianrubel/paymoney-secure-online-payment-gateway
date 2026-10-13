<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualcardSpendingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_spendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtualcard_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 8)->default(0.00);
            $table->string('category')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_spendings');
    }
}
