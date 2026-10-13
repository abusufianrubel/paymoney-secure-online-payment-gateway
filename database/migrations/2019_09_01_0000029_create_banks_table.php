<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table)
        {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('cascade');

            $table->string('bank_name', 191)->nullable();
            $table->string('bank_branch_name', 191)->nullable();
            $table->string('bank_branch_city', 191)->nullable();
            $table->string('bank_branch_address', 191)->nullable();
            $table->string('account_name', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->string('swift_code', 191)->nullable();
            $table->string('is_default', 3)->default('No')->comment('No, Yes');

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
        Schema::dropIfExists('banks');
    }
}
