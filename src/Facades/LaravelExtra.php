<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Facades;
use Illuminate\Support\Facades\Facade;
use Aybarsm\Laravel\Extra\Contracts\LaravelExtraContract;

/**
 * @see LaravelExtraContract
 */
final class LaravelExtra extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelExtraContract::class;
    }
}
