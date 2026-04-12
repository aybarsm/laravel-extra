<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

use Aybarsm\Laravel\Extra\Support\Fluent;

trait HasFluentData
{
    protected static Fluent $data;
    protected static function getData(): Fluent
    {
        if (!isset(static::$data)) static::$data = new Fluent();
        return static::$data;
    }

    protected static function getDataKey(
        string|int|null|iterable|\Stringable $key,
        bool $forceSelf = true,
    ): ?string
    {
        return data_key($key, ($forceSelf ? static::class : null));
    }
}
