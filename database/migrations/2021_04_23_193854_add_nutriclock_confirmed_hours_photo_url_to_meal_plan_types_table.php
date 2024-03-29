<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNutriclockConfirmedHoursPhotoUrlToMealPlanTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meal_plan_types', function (Blueprint $table) {
            $table->boolean('confirmed')->default(false);
            $table->string('confirmedHours')->nullable();
            $table->string('photoUrl')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meal_plan_types', function (Blueprint $table) {

        });
    }
}
