<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Aybarsm\Laravel\Extra\Enums\ConsoleCommandInputType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;

interface ConsoleCommandInputContract extends Arrayable, Jsonable, \JsonSerializable
{
    public static function make(
        SymfonyInputArgument|SymfonyInputOption $input,
        ?int $position = null,
        ?string $commandClass = null,
    ): namespace\ConsoleCommandArgumentContract|namespace\ConsoleCommandOptionContract;

    public function getType(): ConsoleCommandInputType;

    public function getName(): string;

    public function getMode(): int;

    public function getDescription(): string;

    public function getDefault(): mixed;

    public function getSuggestedValues(): array;

    public function getPosition(): ?int;
    public function getCommandClass(): ?string;

    public function isRequired(): bool;
    public function isOptional(): bool;
    public function isArray(): bool;
}
