<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Mixins;

use Aybarsm\Extra\Enums\ModeDirection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/** @mixin Stringable */
final class StringableMixin
{
    final const string BIND = Stringable::class;

    public static function trimRecursive(): \Closure
    {
        return function (
            ?string $characters = null,
            string|ModeDirection $dir = ModeDirection::BOTH,
            string|\Stringable ...$more,
        ): Stringable {
            return new static(Str::trimRecursive($this->value, $characters, $dir, ...$more));
        };
    }

}
