<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rank')->length(4)->comment('最新のランキング');
            $table->integer('most_highest')->length(4)->nullable()->comment('これまでの一番高いランキング');
            $table->string('name', 50)->commnet('選手名');
            $table->integer('age')->length(3)->comment('年齢');
            $table->string('country', 50)->comment('所属国');
            $table->integer('point')->length(6)->commnet('ATPポイント');
            $table->integer('rank_change')->length(6)->commnet('前回ランクからの変化');
            $table->integer('point_change')->length(6)->comment('前回ポイントからの変化');
            $table->string('current_tour_result', 100)->commnet('現在参加している大会の結果');
            $table->string('pre_tour_result', 100)->comment('前回大会の結果');
            $table->integer('next_point')->length(6)->comment('次に勝つと入手できるポイント');
            $table->integer('max_point')->length(6)->commnet('現在参加している大会で全部勝てばどれだけポイントが入手できるか');
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
        Schema::dropIfExists('rankings');
    }
}
