<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Spatie\LaravelData\Data;

final class ArtisanCommandAliasDto extends Data
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public static function from(...$args): static
    {
        return new self($args[0] ?? null);
    }
}
