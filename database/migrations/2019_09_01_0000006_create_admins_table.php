<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onUpdate('cascade')->onDelete('cascade');

            // $table->string('username')->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->unique()->nullable()->default(null);
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->string('status')->default('Active')->comment('Active or Inactive');
            $table->string('picture', 100);
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
