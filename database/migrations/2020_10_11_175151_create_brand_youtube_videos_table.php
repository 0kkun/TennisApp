<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandYoutubeVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_youtube_videos', function (Blueprint $table) {
          $table->increments('id');
          $table->string('title');
          $table->string('url');
          $table->date('post_time');
          $table->integer('brand_id')->unsigned();
          $table->timestamps();

          $table->foreign('brand_id')
          ->references('id')->on('brands')
          ->onDelete('cascade')
          ->onUpdate('cascade');

          $table->unique(['title', 'brand_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_youtube_videos');
    }
}
