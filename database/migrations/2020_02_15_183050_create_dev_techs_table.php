<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevTechsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dev_techs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_dev')->nullable()->unsigned();
            $table->bigInteger('id_tech')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('id_dev', 'fk_id_dev')->references('id')->on('devs')->onDelete('set null');

            $table->foreign('id_tech', 'fk_id_tech')->references('id')->on('techs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dev_techs');
    }
}
