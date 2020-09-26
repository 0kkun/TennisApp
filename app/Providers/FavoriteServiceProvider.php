<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FavoritePlayer\FavoritePlayerService;
use App\Services\FavoritePlayer\FavoritePlayerServiceInterface;

class FavoritePlayerServiceProvider extends ServiceProvider
{
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
        $this->app->bind(FavoritePlayerServiceInterface::class, FavoritePlayerService::class);
    }
}