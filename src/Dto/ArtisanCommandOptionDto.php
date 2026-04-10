<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

final class ArtisanCommandOptionDto extends namespace\ArtisanCommandDefinitionDto
{
    public function __construct(
        bool $isRequired,
        bool $isOptional,
        bool $isArray,
        public readonly bool $isNone,
        public readonly bool $isNegatable,
    ) {
        parent::__construct($isRequired, $isOptional, $isArray);
    }
}
