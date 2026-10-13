<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyUsersTable extends Migration
{
    public function up()
    {
        Schema::create('verify_users', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verify_users');
    }
}
