<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerNewsArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players_news_articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->comment('記事のタイトル');
            $table->string('image')->comment('ニュースのトップ画像');
            $table->string('url')->comment('記事のリンク');
            $table->date('post_time')->comment('記事の投稿日時');
            $table->string('vender', 50)->comment('情報のソース');
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
        Schema::dropIfExists('players_news_articles');
    }
}
