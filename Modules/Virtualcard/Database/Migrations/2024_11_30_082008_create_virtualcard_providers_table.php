<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualcardProvidersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique()->index('virtualcard_providers_name_idx');
            $table->json('currency_id');
            $table->string('type', 9)->default('Manual')->comment('Automated, Manual');
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_providers');
    }
}
