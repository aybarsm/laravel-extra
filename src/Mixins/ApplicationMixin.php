<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Mixins;

use Aybarsm\Laravel\Extra\Facades\LaravelExtra;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

/** @mixin \Illuminate\Foundation\Application */
final class ApplicationMixin
{
    const string BIND = \Illuminate\Foundation\Application::class;

    public static function getArtisanCommandMeta(): \Closure
    {
        return static function (null|string|object $command = null): ?array {
            $meta = LaravelExtra::getArtisanMeta();
            if (blank($command)) return $meta;

            if (is_string($command)) {
                $base = $meta['mapping'][$command] ?? null;
                return $base ? ($meta['commands'][$base] ?? null) : null;
            }

            $class = get_class($command);
            return Arr::first(
                $meta['commands'],
                static fn ($item) => $item['class'] === $class
            );
        };
    }
    public static function getArtisanCommandBaseName(): \Closure
    {
        return static function (string|object $command): ?string {
            return (static::getArtisanCommandMeta($command) ?? [])['name'] ?? null;
        };
    }
    public static function getArtisanCommandClass(): \Closure
    {
        return static function (string|object $command): ?string {
            return (static::getArtisanCommandMeta($command) ?? [])['class'] ?? null;
        };
    }

    public static function getArtisanCommandObject(): \Closure
    {
        return static function (string $command): ?\Illuminate\Console\Command {
            $baseName = static::getArtisanCommandBaseName($command);
            if (!$baseName) return null;
            return Artisan::all()[$baseName] ?? null;
        };
    }
}
