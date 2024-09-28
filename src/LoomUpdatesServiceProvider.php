<?php

namespace mlusas\LaravelLoomUpdates;

use Illuminate\Support\Facades\Route;
use mlusas\LaravelLoomUpdates\Models\LoomUrl;
use mlusas\LaravelLoomUpdates\Commands\ListLoomUrls;
use mlusas\LaravelLoomUpdates\Commands\StoreLoomUrls;
use Illuminate\Support\ServiceProvider;

class LoomUpdatesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'loom-updates');

        $this->publishes([
            __DIR__.'/../config/loom-updates.php' => config_path('loom-updates.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ListLoomUrls::class,
                StoreLoomUrls::class,
            ]);
        }

        Route::get('/loom-videos', function () {
            $loomUrls = LoomUrl::all();
            return view('loom-updates::loom-viewer', compact('loomUrls'));
        })->name('loom-videos');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/loom-updates.php', 'loom-updates'
        );
    }    
}