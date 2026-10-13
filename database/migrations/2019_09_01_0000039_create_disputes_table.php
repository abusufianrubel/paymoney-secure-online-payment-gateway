<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disputes', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('claimant_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('defendant_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('reason_id')->constrained('reasons')->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('code', 45)->nullable();
            $table->string('status', 7)->default('Open')->comment('Open, Closed, Solved');
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
        Schema::dropIfExists('disputes');
    }
}
