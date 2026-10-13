<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('admin_id')->nullable()->constrained('admins')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ticket_reply_id')->nullable()->constrained('ticket_replies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('filename')->nullable();
            $table->string('originalname')->nullable();
            $table->string('type')->nullable();

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
        Schema::dropIfExists('files');
    }
}
