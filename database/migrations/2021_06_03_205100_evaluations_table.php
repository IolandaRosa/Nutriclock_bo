<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId');
            $table->integer('question1')->unsigned();
            $table->integer('question2')->unsigned();
            $table->integer('question3')->unsigned();
            $table->integer('question4')->unsigned();
            $table->integer('question5')->unsigned();
            $table->integer('question6')->unsigned();
            $table->integer('question7')->unsigned();
            $table->integer('question8')->unsigned();
            $table->integer('question9')->unsigned();
            $table->integer('question10')->unsigned();
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
        Schema::dropIfExists('evaluations');
    }
}
