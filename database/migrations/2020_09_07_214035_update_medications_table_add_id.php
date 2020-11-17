<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMedicationsTableAddId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER Table medications add id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');
        // DB::statement('ALTER Table medications add id SERIAL NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medications', function (Blueprint $table) {
            //
        });
    }
}
