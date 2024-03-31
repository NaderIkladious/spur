<?php

namespace Spur\Spur\Providers;


use Illuminate\Support\ServiceProvider;
use Spur\Spur\Console\Commands;

class SpurServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            Commands\SpurFetch::class,
            Commands\SpurAdd::class
        ]);
    }

    /**
     * Bootstrap services.
     */

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/spur.php' => config_path('spur.php'),
        ], 'spur-config');
    }
}
