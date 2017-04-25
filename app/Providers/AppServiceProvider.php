<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production') {
            $url->forceScheme('https');
        }

        \Request::macro('intersectKeys', function ($keys) {
            return array_intersect_key($this->all(), array_flip($keys));
        });

        view()->composer([
                'layouts.partials.nav'
            ], function($view) {
            $view->with('route', Route::currentRouteName());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
        
        $this->app->bind(\App\Image\ImageHandler::class, function() {
            return new \App\Image\InterventionImage();
        });
    }
}
