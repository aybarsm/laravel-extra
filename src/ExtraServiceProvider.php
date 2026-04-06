<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Illuminate\Support\ServiceProvider;

final class ExtraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/extra.php',
            'extra'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/extra.php' => config_path('extra.php'),
            ], 'extra-config');
        }
    }
}
