<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Aybarsm\Extra\Enums\ModeType;

interface DiscoveredValueContract
{
    public function getValue(): mixed;
    public function is(ModeType|string ...$of): bool;
    public function convertable(ModeType|string $to): bool;
    public function convert(ModeType|string $to): static;
}
