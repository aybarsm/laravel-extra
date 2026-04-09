<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Mixins;

use Aybarsm\Laravel\Extra\Facades\Extra;
use Illuminate\Support\Facades\Artisan;

/** @mixin \Illuminate\Foundation\Application */
final class ApplicationMixin
{
    const string BIND = \Illuminate\Foundation\Application::class;

    public static function getArtisanCommandBase(): \Closure
    {
        return static function (string $command): ?string {
            return (Extra::getArtisanMeta()['mapping'] ?? [])[$command] ?? null;
        };
    }
    public static function getArtisanCommandObject(): \Closure
    {
        return static function (string $command): ?\Illuminate\Console\Command {
            $base = static::getArtisanCommandBase($command);
            return $base ? (Artisan::all()[$base] ?? null) : null;
        };
    }

    public static function getArtisanCommandClass(): \Closure
    {
        return static function (string $command): ?string {
            $base = static::getArtisanCommandBase($command);
            return $base ? (Extra::getArtisanMeta()['commands'][$base]['class'] ?? null) : null;
        };
    }
}
