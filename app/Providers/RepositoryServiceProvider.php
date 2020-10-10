<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * モデル名を配列にまとめる
     *
     * @var array
     */
    public $models = [
        'Players',
        'FavoritePlayers',
        'AtpRankings',
        'NewsArticles',
        'TourInformations',
        'Brands',
        'FavoriteBrands',
        'BrandNewsArticles',
        'YoutubeVideos'
    ];


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // リポジトリをモデル名でBindする
        foreach ($this->models as $model) {
            $this->app->bind(
                "App\Repositories\Contracts\\{$model}Repository",
                "App\Repositories\Eloquents\Eloquent{$model}Repository"
            );
        }
    }
}
