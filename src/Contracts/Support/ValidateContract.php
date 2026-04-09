<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Support;

use Aybarsm\Laravel\Extra\Enums\ModeMatch;

interface ValidateContract
{
    public static function flagsHas(int $flags, int|array $of, ModeMatch $match = ModeMatch::ANY): bool;
}
