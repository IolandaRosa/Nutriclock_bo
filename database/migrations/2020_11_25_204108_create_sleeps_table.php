<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSleepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sleeps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('date');
            $table->integer('userId');
            $table->string('wakeUpTime');
            $table->string('sleepTime');
            $table->boolean('hasWakeUp')->default(false);
            $table->softDeletes();
            $table->string('activities')->nullable();
            $table->string('motives')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sleeps');
    }
}
