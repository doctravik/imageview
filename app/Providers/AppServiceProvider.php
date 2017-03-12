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

        view()->composer([
                'layouts.partials.nav'
            ], function($view) {
            $view->with('route', Route::currentRouteName());
        });

        \App\Photo::observe(\App\Observers\PhotoObserver::class);

        \DB::listen(function ($query) {
            $sqlParts = explode('?', $query->sql);
            $bindings = $query->connection->prepareBindings($query->bindings);
            $pdo = $query->connection->getPdo();
            $sql = array_shift($sqlParts);
            foreach ($bindings as $i => $binding) {
                $sql .= $pdo->quote($binding) . $sqlParts[$i];
            }
            
            \Log::info($sql);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
