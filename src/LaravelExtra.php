<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra;

use Aybarsm\Laravel\Extra\Concerns\HasSupportAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Fluent;

final class LaravelExtra implements namespace\Contracts\LaravelExtraContract
{
    use HasSupportAccess;
    private static Fluent $data;

    public function __construct()
    {
        $this->registerMixins();
        Event::dispatch(namespace\Events\LaravelExtraInitialised::class, $this);
    }

    public function getCache(): ?array
    {
        return $this->getCacheStore()?->get($this->getCacheKey(), []);
    }

    private static function data(): Fluent
    {
        if (!isset(self::$data)) self::$data = new Fluent();
        return self::$data;
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

    private function registerMixins(): void
    {
        if (self::data()->get('mixins.registered')){
            return;
        }

        $cache = $this->getCache() ?? [];
        if (!array_key_exists('mixins', $cache)) {
            $cache['mixins'] = $this->buildMixinDiscovery();
        }

        $this->putCache($cache);

        foreach($cache['mixins'] as $bind => $mixins){
            foreach($mixins as $mixin => $replace){
                $bind::mixin(app()->make($mixin), $replace);
            }
        }

        self::data()->set('mixins.registered', true);
    }

    private function buildMixinDiscovery(): array
    {
        $bindReflections = [];
        $ret = [];
        foreach(self::config('mixins', []) as $class){
            self::throw_if(
                !class_exists($class),
                sprintf('Mixin class [%s] does not exist.', $class)
            );

            $ref = new \ReflectionClass($class);
            self::throw_if(
                !$ref->isInstantiable(),
                sprintf('Mixin class [%s] is not instantiable.', $class)
            );
            self::throw_if(
                !$ref->hasConstant('BIND'),
                sprintf('Mixin class [%s] does not have a binding class constant.', $class)
            );

            $bind = $ref->getConstant('BIND');
            self::throw_if(
                !class_exists($class),
                sprintf('Mixin class [%s] defined binding class [%s] does not exist.', $class, $bind)
            );

            $replace = $ref->hasConstant('REPLACE') && $ref->getConstant('REPLACE') === true;

            $ref = $bindReflections[$bind] ?? ($bindReflections[$bind] = new \ReflectionClass($bind));
            $method = $ref->hasMethod('mixin') ? $ref->getMethod('mixin') : null;
            self::throw_if(
                ! $method || ! $method->isStatic() || ! $method->isPublic(),
                sprintf('Mixin class [%s] defined binding class [%s] does not have a valid mixin method.', $class, $bind)
            );

            if (!isset($ret[$bind])) $ret[$bind] = [];
            $ret[$bind][$class] = $replace;
        }

        return $ret;
    }

    private static function throw_if(mixed $condition, mixed $message): void
    {
        $condition = value($condition);
        throw_if(
            $condition,
            namespace\Exceptions\LaravelExtraException::class,
            value($message, $condition)
        );
    }
}
