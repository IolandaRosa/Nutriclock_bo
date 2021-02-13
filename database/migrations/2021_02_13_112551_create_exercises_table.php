<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('met',5,1);
            $table->string('name');
            $table->integer('startTime')->default(0);
            $table->integer('endTime')->default(0);
            $table->integer('duration')->default(0);
            $table->integer('burnedCalories')->default(0);
            $table->string('date');
            $table->integer('userId');
            $table->integer('exerciseId');
            $table->enum('type', ['H','E'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercises');
    }
}
