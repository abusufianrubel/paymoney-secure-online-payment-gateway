<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//pm - 1.7
class CreateDocumentVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_verifications', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->string('verification_type', 11)->nullable()->comment('address, identity');
            $table->string('identity_type', 17)->nullable()->comment('driving_license, passport, national_id');
            $table->string('identity_number')->nullable();
            $table->string('status', 11)->default('pending')->comment('pending, approved, rejected');
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
        Schema::dropIfExists('document_verifications');
    }
}
