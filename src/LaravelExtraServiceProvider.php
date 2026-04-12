<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Illuminate\Support\ServiceProvider;

final class LaravelExtraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->registerBindings();
        $this->app->booted(
            static fn () => app(namespace\Contracts\LaravelExtraContract::class)
        );
    }

    public function boot(): void
    {
        $this->bootPublishes();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-extra.php',
            'laravel-extra'
        );
    }

    private function registerBindings(): void
    {
        $this->app->singletonIf(
            namespace\Contracts\LaravelExtraContract::class,
            namespace\LaravelExtra::class,
        );
        $this->app->alias(
            namespace\Contracts\LaravelExtraContract::class,
            'laravel-extra',
        );

        $this->app->singletonIf(
            namespace\Contracts\Support\ValidateContract::class,
            namespace\Support\Validate::class,
        );
        $this->app->alias(
            namespace\Contracts\Support\ValidateContract::class,
            'laravel-extra.support.validate',
        );

        $this->app->bindIf(
            namespace\Contracts\Dto\ConsoleCommandContract::class,
            namespace\Dto\ConsoleCommand::class,
        );
        $this->app->bindIf(
            namespace\Contracts\Dto\ConsoleCommandArgumentContract::class,
            namespace\Dto\ConsoleCommandArgument::class,
        );
        $this->app->bindIf(
            namespace\Contracts\Dto\ConsoleCommandOptionContract::class,
            namespace\Dto\ConsoleCommandOption::class,
        );
    }

    private function bootPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-extra.php' => config_path('laravel-extra.php'),
        ], 'laravel-extra-config');
    }
}
