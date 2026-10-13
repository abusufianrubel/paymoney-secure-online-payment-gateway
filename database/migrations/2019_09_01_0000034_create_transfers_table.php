<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->string('uuid', 13)->nullable()->comment('Unique ID (For Each Transfer)');
            $table->decimal('fee', 20, 8)->nullable()->default(0.00000000);
            $table->decimal('amount', 20, 8)->nullable()->default(0.00000000);
            $table->text('note')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('status', 11)->comment('Pending, Success, Refund, Blocked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
