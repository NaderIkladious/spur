<?php

namespace Naderikladious\Spur\Providers;


use Illuminate\Support\ServiceProvider;
use Naderikladious\Spur\Console\Commands;

class SpurServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->commands([
            Commands\SpurFetch::class
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
