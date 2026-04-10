<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

final class ArtisanCommandArgumentDto extends namespace\ArtisanCommandDefinitionDto
{
    public function __construct(
        bool $isRequired,
        bool $isOptional,
        bool $isArray,
    ) {
        parent::__construct($isRequired, $isOptional, $isArray);
    }
}
