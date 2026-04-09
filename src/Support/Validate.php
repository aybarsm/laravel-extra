<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Support;

use Aybarsm\Laravel\Extra\Contracts\Support\ValidateContract;
use Aybarsm\Laravel\Extra\Enums\ModeMatch;
use Illuminate\Support\Arr;

final class Validate implements ValidateContract
{
    public static function flagsHas(int $flags, int|array $of, ModeMatch $match = ModeMatch::ANY): bool
    {
        foreach(Arr::wrap($of) as $flag){
            if (! is_null($result = $match->early(($flags & $flag) !== 0))){
                return $result;
            }
        }

        return $match->final();
    }
}
