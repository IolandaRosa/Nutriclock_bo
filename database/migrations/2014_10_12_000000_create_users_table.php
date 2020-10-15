<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('gender', ['MALE', 'FEMALE','NONE']);
            $table->decimal('weight', 4, 1)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['ADMIN', 'PROFESSIONAL', 'INTERN', 'PATIENT']);
            $table->boolean('active')->default(true);
            $table->string('avatarUrl')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->integer('professionalCategoryId')->unsigned()->nullable();
            $table->longText('diseases')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('ufcs', function (Blueprint $table) {
             $table->increments('id');
             $table->string('name');
             $table->softDeletes();
             $table->timestamps();
        });
        Schema::create('professional_categories', function (Blueprint $table) {
             $table->id();
             $table->string('name');
             $table->softDeletes();
             $table->timestamps();
        });
        Schema::create('medications', function (Blueprint $table) {
             $table->integer('user_id')->unsigned();
             $table->foreign('user_id')->references('id')->on('users');
             $table->string('name');
             $table->string('timesADay')->nullable();
             $table->string('timesAWeek')->nullable();
             $table->primary(['user_id', 'name']);
             $table->softDeletes();
             $table->timestamps();
        });
        Schema::create('users_ufc', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('ufc_id')->unsigned();
            $table->foreign('ufc_id')->references('id')->on('ufcs');
            $table->primary(['user_id', 'ufc_id']);
            $table->softDeletes();
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
        Schema::dropIfExists('users_ufc');
        Schema::dropIfExists('medication');
        Schema::dropIfExists('users');
        Schema::dropIfExists('professional_categories');
        Schema::dropIfExists('ufc');
    }
}
