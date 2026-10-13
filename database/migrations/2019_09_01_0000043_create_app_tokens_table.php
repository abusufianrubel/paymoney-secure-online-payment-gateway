<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('merchant_apps')->onUpdate('cascade')->onDelete('cascade');
            $table->string('token',100);
            $table->string('expires_in',20);
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
        Schema::dropIfExists('app_tokens');
    }
}
