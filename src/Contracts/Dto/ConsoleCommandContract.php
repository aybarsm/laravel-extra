<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandHas;
use Illuminate\Console\Command as LaravelCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandCollectionContract;
interface ConsoleCommandContract
{
    public static function make(
        ConsoleCommandContract|SymfonyCommand|LaravelCommand $command
    ): ConsoleCommandContract;
    public function getClass(): string;

    public function getName(): string;

    public function getDescription(): string;

    public function getAliases(): array;
    public function getArguments(): array;
    public function getOptions(): array;
    public function getNameMapping(): array;
    public function has(ConsoleCommandHas|string $of): bool;
    public function hasAny(ConsoleCommandHas|string ...$of): bool;
    public function hasAll(ConsoleCommandHas|string ...$of): bool;
    public function hasAlias(): bool;
    public function hasArgument(): bool;
    public function hasArgumentRequired(): bool;
    public function hasArgumentOptional(): bool;
    public function hasArgumentArray(): bool;
    public function hasOption(): bool;
    public function hasOptionRequired(): bool;
    public function hasOptionOptional(): bool;
    public function hasOptionArray(): bool;
    public function hasOptionNone(): bool;
    public function hasOptionNegatable(): bool;

    public function hasInputRequired(): bool;
    public function hasInputOptional(): bool;
    public function hasInputArray(): bool;
}
