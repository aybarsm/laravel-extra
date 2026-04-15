<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

use Aybarsm\Laravel\Extra\Support\Fluent;

trait HasFluentMetaData
{
    protected static Fluent $_metaData_;
    protected static function getMetaData(): Fluent
    {
        if (!isset(static::$_metaData_)) static::$_metaData_ = new Fluent();
        return static::$_metaData_;
    }

    protected static function getMetaDataKey(
        string|int|null|iterable|\Stringable $key,
        bool $forceSelf = true,
    ): ?string
    {
        return data_key($key, ($forceSelf ? static::class : null));
    }
}
