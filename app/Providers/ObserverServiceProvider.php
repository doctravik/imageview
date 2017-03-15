<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Photo::observe(\App\Observers\PhotoObserver::class);
        \App\Album::observe(\App\Observers\AlbumObserver::class);
        \App\Thumbnail::observe(\App\Observers\ThumbnailObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
