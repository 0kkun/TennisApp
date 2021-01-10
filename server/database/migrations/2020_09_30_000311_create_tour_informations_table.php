<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('category',100);
            $table->string('location',100)->nullable();
            $table->string('surface',100)->nullable();
            $table->string('draw_num',50)->nullable();
            $table->year('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->unique(['name','year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_informations');
    }
}
