<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Aybarsm\Laravel\Extra\Enums\ConsoleCommandInputType;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;

interface ConsoleCommandInputContract
{
    public static function make(
        SymfonyInputArgument|SymfonyInputOption $input,
        ?int $pos = null,
        ?string $command = null,
    ): namespace\ConsoleCommandArgumentContract|namespace\ConsoleCommandOptionContract;

    public function getType(): ConsoleCommandInputType;

    public function getName(): string;

    public function isRequired(): bool;
    public function isOptional(): bool;
    public function isArray(): bool;
}
