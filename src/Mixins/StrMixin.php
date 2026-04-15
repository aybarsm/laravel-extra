<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Mixins;

use Aybarsm\Extra\Enums\ModeDirection;
use Aybarsm\Extra\Support\Str as ExtraSupportStr;

/** @mixin \Illuminate\Support\Str */
final class StrMixin
{
    const string BIND = \Illuminate\Support\Str::class;

    public static function trimRecursive(): \Closure
    {
        return static function (
            string|\Stringable $subject,
            ?string $characters = null,
            string|ModeDirection $dir = ModeDirection::BOTH,
            string|\Stringable ...$more,
        ): string {
            return ExtraSupportStr::trimRecursive($subject, $characters, $dir, ...$more);
        };
    }
}
