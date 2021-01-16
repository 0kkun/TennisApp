<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('大会名');
            $table->string('location', 100)->comment('開催場所');
            $table->string('surface', 50)->comment('コートのサーフェス');
            $table->string('category', 50)->comment('ランクのカテゴリ');
            $table->year('year')->comment('開催年');
            $table->date('start_date')->comment('大会が始まる日');
            $table->date('end_date')->comment('大会が終わる日');
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
        Schema::dropIfExists('tour_schedules');
    }
}
