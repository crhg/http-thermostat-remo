<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThermostatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thermostats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('on_off');
            $table->unsignedTinyInteger('heating_cooling');
            $table->unsignedDecimal('target_temperature', 3, 1);
            $table->unsignedDecimal('target_humidity', 5, 1);
            $table->timestamps();

            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thermostats');
    }
}
