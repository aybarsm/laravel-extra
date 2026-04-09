<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

use Aybarsm\Laravel\Extra\Contracts\Support\ValidateContract;

trait HasSupportAccess
{
    public static function validate(): ValidateContract
    {
        return app('laravel-extra.support.validate');
    }
}
