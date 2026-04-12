<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Concerns\HasFluentData;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandInputContract;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandInputType;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandArgumentContract;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandOptionContract;
abstract class AbstractConsoleCommandInput implements ConsoleCommandInputContract
{
    public readonly ConsoleCommandInputType $type;
    public function __construct(
        ConsoleCommandInputType|string $type,
        public readonly string         $name,
        public readonly int            $mode,
        public readonly ?string        $description = null,
        public readonly mixed          $default = null,
        public readonly array          $suggestedValues = [],
        public readonly ?int           $position = null,
        public readonly ?string        $commandClass = null,
    ) {
        $this->type = ConsoleCommandInputType::make($type);
    }

    public static function make(
        SymfonyInputArgument|SymfonyInputOption $input,
        ?int $position = null,
        ?string $commandClass = null,
    ): ConsoleCommandArgumentContract|ConsoleCommandOptionContract
    {
        $isArg = is_a($input, SymfonyInputArgument::class);
        $type = (is_a($input, SymfonyInputArgument::class) ? ConsoleCommandInputType::ARGUMENT : ConsoleCommandInputType::OPTION);

        $ref = new \ReflectionObject($input);
        $args = [
            'type' => $type,
            'name' => $input->getName(),
            'mode' => value($ref->getProperty('mode')->getValue($input)),
            'description' => $input->getDescription(),
            'default' => $input->getDefault(),
            'shortcut' => $isArg ? null : $input->getShortcut(),
            'suggestedValues' => value($ref->getProperty('suggestedValues')->getValue($input)),
            'position' => $position,
            'commandClass' => $commandClass,
        ];

        return app()->makeWith($type->getAbstractClass(), $args);
    }

    public function getType(): ConsoleCommandInputType
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getSuggestedValues(): array
    {
        return $this->suggestedValues;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getCommandClass(): ?string
    {
        return $this->commandClass;
    }

    public function isRequired(): bool
    {
        return flags_has($this->mode, $this->type->getRequiredFlag());
    }

    public function isOptional(): bool
    {
        return flags_has($this->mode, $this->type->getOptionalFlag());
    }

    public function isArray(): bool
    {
        return flags_has($this->mode, $this->type->getArrayFlag());
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'mode' => $this->getMode(),
            'description' => $this->getDescription(),
            'default' => $this->getDefault(),
            'suggestedValues' => $this->getSuggestedValues(),
            'position' => $this->getPosition(),
            'commandClass' => $this->getCommandClass(),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
