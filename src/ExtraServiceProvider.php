<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Illuminate\Support\ServiceProvider;

final class ExtraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->registerBindings();
        $this->app->booted(
            static fn () => app(namespace\Contracts\ExtraContract::class)
        );
    }

    public function boot(): void
    {
        $this->bootPublishes();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/extra.php',
            'extra'
        );
    }

    private function registerBindings(): void
    {
        $this->app->singletonIf(
            namespace\Contracts\ExtraContract::class,
            namespace\Extra::class,
        );
        $this->app->alias(
            namespace\Contracts\ExtraContract::class,
            'extra',
        );

        $this->app->singletonIf(
            namespace\Contracts\Support\ValidateContract::class,
            namespace\Support\Validate::class,
        );
        $this->app->alias(
            namespace\Contracts\Support\ValidateContract::class,
            'extra.support.validate',
        );
    }

    private function bootPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/extra.php' => config_path('extra.php'),
        ], 'extra-config');
    }
}
