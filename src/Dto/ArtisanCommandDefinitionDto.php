<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Spatie\LaravelData\Data;

class ArtisanCommandDefinitionDto extends Data
{
    public function __construct(
        public readonly bool $isRequired,
        public readonly bool $isOptional,
        public readonly bool $isArray,
    ) {
    }
}
