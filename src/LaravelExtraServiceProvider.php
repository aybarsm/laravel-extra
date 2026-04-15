<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class LaravelExtraServiceProvider extends ServiceProvider
{
//    public function newPackage(): ProviderPackage
//    {
//        return new namespace\Custom\Spatie\LaravelPackageTools\ProviderPackage();
//    }
//
//    public function registeringPackage(): void
//    {
//        namespace\Events\ProviderRegistering::dispatch($this);
//    }
//
//    public function packageRegistered(): void
//    {
//        namespace\Events\ProviderRegistered::dispatch($this);
//    }
//
//    public function bootingPackage(): void
//    {
//        namespace\Events\ProviderBooting::dispatch($this);
//    }
//
//    public function packageBooted(): void
//    {
//        namespace\Events\ProviderBooted::dispatch($this);
//    }

    public function register(): void
    {
        $this->registerConfig();
        $this->registerBindings();
        $this->registerEvents();
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

        $this->app->singletonIf(
            abstract: namespace\Contracts\Dto\ConsoleCommandCollectionContract::class,
            concrete: static fn () => new namespace\Dto\ConsoleCommandCollection(array_map(
                static fn ($command) => namespace\Dto\ConsoleCommand::make($command),
                Artisan::all()
            )),
        );
    }

    private function registerEvents(): void
    {
        Event::listen(
            'bootstrapped: Illuminate\Foundation\Bootstrap\BootProviders',
            static function(): void {
                app(namespace\Contracts\LaravelExtraContract::class);
            }
        );
    }

    private function bootPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-extra.php' => config_path('laravel-extra.php'),
        ], 'laravel-extra-config');
    }

//    public function configurePackage(ProviderPackage $package): void
//    {
//        $package->name('laravel-extra')
//            ->hasConfigFile()
//            ;
//
//    }
}
