<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Facades;
use Illuminate\Support\Facades\Facade;
use Aybarsm\Laravel\Extra\Contracts\ExtraContract;

/**
 * @see ExtraContract
 */
final class Extra extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ExtraContract::class;
    }
}
