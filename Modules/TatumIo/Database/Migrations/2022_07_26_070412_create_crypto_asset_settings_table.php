<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptoAssetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto_asset_settings', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('currency_id')->unique()->constrained('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('crypto_provider_id')->constrained('crypto_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');
            $table->string('network', 30)->unique()->index('crypto_asset_settings_network_idx')->comment('Networks/Cryto Curencies - BTC,LTC,DT etc.');
            $table->text('network_credentials');
            $table->string('status', 11)->default('Active')->comment('Active/Inactive');
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
        Schema::dropIfExists('crypto_asset_settings');
    }
}
