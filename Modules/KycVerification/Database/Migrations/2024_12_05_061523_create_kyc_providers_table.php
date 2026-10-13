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
        Schema::create('kyc_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('alias', 100)->unique();
            $table->string('is_default', 5)->default('No')->comment('Yes, No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_providers');
    }
};
