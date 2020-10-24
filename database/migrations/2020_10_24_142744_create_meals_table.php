<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->decimal('quantity', 6, 2)->nullable();
            $table->dateTime('date')->nullable();
            $table->string('time');
            $table->string('foodPhotoUrl')->nullable();
            $table->string('nutritionalInfoPhotoUrl')->nullable();
            $table->enum('type', ['P','A','J','S','O','L'])->nullable();
            $table->string('relativeUnit')->nullable();
            $table->string('numericUnit')->nullable();
            $table->longText('observations')->nullable();
            $table->string('userId');
        });

        Schema::create('nutritional_infos', function (Blueprint $table) {
             $table->id();
             $table->timestamps();
             $table->softDeletes();
             $table->string('label');
             $table->string('value');
             $table->string('unit');
             $table->string('mealId');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals');
        Schema::dropIfExists('nutritional_infos');
    }
}
