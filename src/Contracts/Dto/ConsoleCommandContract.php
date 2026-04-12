<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandHas;
use Illuminate\Console\Command as LaravelCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

interface ConsoleCommandContract
{
    public static function make(
        SymfonyCommand|LaravelCommand $command
    ): ConsoleCommandContract;

    public function getClass(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getAliases(): array;
    public function getArguments(): array;
    public function getOptions(): array;

    public function has(ConsoleCommandHas|string $of): bool;

    public function hasAny(ConsoleCommandHas|string ...$of): bool;
    public function hasAll(ConsoleCommandHas|string ...$of): bool;

}
