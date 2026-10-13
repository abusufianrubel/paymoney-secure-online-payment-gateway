<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Virtualcard\Entities\VirtualcardProvider;

class CreateVirtualcardHoldersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtualcard_holders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(VirtualcardProvider::class)->nullable()->constrained()->onUpdate('cascade')->onDelete('restrict');
            $table->string('card_holder_type', 11)->default('Individual')->index('virtualcard_holders_card_holder_type_idx')->comment('Individual or Business');

            $table->string('business_name', 30)->nullable();
            $table->string('business_id_number', 50)->nullable();

            $table->string('gender', 11)->nullable()->comment('required if card_holder_type is individual');
            $table->date('date_of_birth')->nullable();
            $table->string('verification_document_type', 30)->nullable()->comment('required if card_holder_type is individual. Passport, NID, Driving License');
            $table->string('verification_document_id_number', 30)->nullable()->comment('required if card_holder_type is individual');
            $table->string('verification_document_image_front', 50)->nullable()->comment('required if card_holder_type is individual');
            $table->string('verification_document_image_back', 50)->nullable()->comment('required if card_holder_type is individual');

            $table->string('first_name', 30)->nullable()->comment('required if card_holder_type is individual');
            $table->string('last_name', 30)->nullable()->comment('required if card_holder_type is individual');
            $table->string('country', 60)->index('virtualcard_holders_country_idx');
            $table->string('state', 60)->nullable();
            $table->string('city', 60);
            $table->string('postal_code', 30);
            $table->string('address', 191);
            $table->string('status', 11)->default('Pending')->comment('Pending,Approved,Rejected');

            $table->string('api_holder_id', 50)->nullable()->comment('Provider holder id');
            $table->longText('api_holder_response')->nullable()->comment('provider holder response');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualcard_holders');
    }
}
