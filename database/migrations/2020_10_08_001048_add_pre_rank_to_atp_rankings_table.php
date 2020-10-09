<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreRankToAtpRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('atp_rankings', function (Blueprint $table) {
            $table->integer('pre_rank')->nullable()->comment('前回ランク');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('atp_rankings', function (Blueprint $table) {
            $table->dropColumn('pre_rank');
        });
    }
}
