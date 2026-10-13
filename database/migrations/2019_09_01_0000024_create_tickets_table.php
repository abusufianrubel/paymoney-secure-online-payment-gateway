<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('admin_id')->nullable()->constrained('admins')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ticket_status_id')->nullable()->constrained('ticket_statuses')->onUpdate('cascade')->onDelete('cascade');

            $table->string('subject', 100)->nullable();
            $table->longText('message')->nullable();
            $table->string('code', 45)->nullable();
            $table->string('priority', 7)->default('Low')->comment('Low, Normal, High');
            $table->timestamp('last_reply')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
