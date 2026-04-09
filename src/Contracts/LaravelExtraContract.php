<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts;

use Aybarsm\Laravel\Extra\Contracts\Support\ValidateContract;

interface LaravelExtraContract
{
    public function getArtisanMeta(): array;
    public static function validate(): ValidateContract;
}
