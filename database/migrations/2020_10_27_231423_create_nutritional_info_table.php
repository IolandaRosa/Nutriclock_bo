<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutritional_info_static', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->string('code');
            $table->primary('code');
            $table->string('name');
            $table->string('energy_kcal')->nullable();
            $table->string('energy_kJ')->nullable();
            $table->string('water_g')->nullable();
            $table->string('protein_g')->nullable();
            $table->string('fats_g')->nullable();
            $table->string('carbo_hidrats_g')->nullable();
            $table->string('fiber_g')->nullable();
            $table->string('colesterol_mg')->nullable();
            $table->string('vitA_mg')->nullable();
            $table->string('vitD_pg')->nullable();
            $table->string('tiamina_mg')->nullable();
            $table->string('riboflavina_mg')->nullable();
            $table->string('niacina_mg')->nullable();
            $table->string('vitB6_mg')->nullable();
            $table->string('vit_B12_pg')->nullable();
            $table->string('vitC_mg')->nullable();
            $table->string('na_mg')->nullable();
            $table->string('k_mg')->nullable();
            $table->string('ca_mg')->nullable();
            $table->string('p_mg')->nullable();
            $table->string('mg_mg')->nullable();
            $table->string('fe_mg')->nullable();
            $table->string('zn_mg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nutritional_info_static');
    }
}
