<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_settings', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('type');
            $table->string('email', 191)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('crypto_address', 191)->nullable();
            $table->string('account_name',191)->nullable();
            $table->string('account_number',191)->nullable();
            $table->string('bank_branch_name',191)->nullable();
            $table->string('bank_branch_city',191)->nullable();
            $table->string('bank_branch_address', 191)->nullable();
            $table->foreignId('country')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->string('swift_code', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->boolean('default_payout')->default(false)->comment('0=not default, 1=default');
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
        Schema::dropIfExists('payout_settings');
    }
}
