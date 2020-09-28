<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BasicServiceProvider extends ServiceProvider
{
    /**
     * サービス名を配列にまとめる
     *
     * @var array
     */
    public $services = [
        'FavoritePlayer',
        'Top',
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
        // サービス名でBindする
        // MEMO: まとめてbindする際、\は2本入れとかないとエラーが出る
        foreach ($this->services as $service) {
            $this->app->bind(
                "App\\Services\\{$service}\\{$service}ServiceInterface",
                "App\\Services\\{$service}\\{$service}Service"
            );
        }
    }
}