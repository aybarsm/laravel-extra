<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Aybarsm\Laravel\Extra\Concerns\HasFluentMetaData;
use Aybarsm\Laravel\Extra\Concerns\Manager\ProcessesMixins;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

final class LaravelExtra implements Contracts\LaravelExtraContract
{
    use HasFluentMetaData, ProcessesMixins;
    private const array CONF = [
        'bind' => [
            Contracts\Dto\ConsoleCommandArgumentContract::class => Dto\ConsoleCommandArgument::class,
            Contracts\Dto\ConsoleCommandOptionContract::class => Dto\ConsoleCommandOption::class,
            Contracts\Dto\ConsoleCommandContract::class => Dto\ConsoleCommand::class,
        ],
        'singleton' => [

        ],
    ];

    public function __construct()
    {
//        Event::listen(
//            'bootstrapped: Illuminate\Foundation\Bootstrap\BootProviders',
//            static function(): void {
//                app(namespace\Contracts\LaravelExtraContract::class);
//            }
//        );
//        $this->registerMixins();
    }

    public static function register(): void
    {
        if (self::getMetaData()->get('registered')) return;

        foreach(self::CONF['bind'] as $abstract => $concrete) {
            app()->bind($abstract, $concrete);
        }

        self::getMetaData()->set('registered', true);
    }

    public function getCache(): ?array
    {
        return $this->getCacheStore()?->get($this->getCacheKey(), []);
    }

    private function config(string $key, mixed $default = null, bool $forcePrefix = true): mixed
    {
        $key = str($key)->split('#\.#', -1, PREG_SPLIT_NO_EMPTY)->toArray();
        if ($forcePrefix && $key[0] !== 'laravel-extra') {
            array_unshift($key, 'laravel-extra');
        }
        $key = implode('.', $key);
        return filled($ret = value(config($key))) ? $ret : $default;
    }

    private function getCacheKey(): string
    {
        return self::config('cache.key', 'laravel-extra');
    }

    private function getCacheStore(): ?\Illuminate\Contracts\Cache\Repository
    {
        $isEnabled = self::config('cache.enabled', app()->isProduction());
        if (!$isEnabled) return null;

        $store = self::config('cache.store', Cache::getStore());
        return Cache::store($store);
    }

    private function putCache(array $context): void
    {
        $this->getCacheStore()?->forever($this->getCacheKey(), $context);
    }

    private static function throw_if(mixed $condition, mixed $message): void
    {
        throw_if(
            value($condition),
            namespace\Exceptions\LaravelExtraException::class,
            value($message, $condition)
        );
    }
}
