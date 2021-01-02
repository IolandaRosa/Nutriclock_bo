<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('senderId');
            $table->string('senderName');
            $table->string('senderPhotoUrl')->nullable();
            $table->integer('receiverId');
            $table->string('receiverName');
            $table->string('receiverPhotoUrl')->nullable();
            $table->longText('message');
            $table->integer('refMessageId')->nullable();
            $table->boolean('read')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
