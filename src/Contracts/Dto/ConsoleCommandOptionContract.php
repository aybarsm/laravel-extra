<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

interface ConsoleCommandOptionContract extends namespace\ConsoleCommandInputContract
{
    public function isNone(): bool;
    public function isNegatable(): bool;

    public function getShortcut(): ?string;
}
