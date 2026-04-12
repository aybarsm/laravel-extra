<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Concerns\HasFluentData;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandInputContract;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandInputType;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;
abstract final class ConsoleCommandInput implements ConsoleCommandInputContract
{
    use HasFluentData;
    public readonly ConsoleCommandInputType $type;
    public function __construct(
        ConsoleCommandInputType|string $type,
        public readonly string         $name,
        public readonly int            $mode,
        public readonly ?string        $description = null,
        public readonly mixed          $default = null,
        public readonly ?string        $shortcut = null,
        public readonly array          $suggestedValues = [],
        public readonly ?int           $pos = null,
        public readonly ?string        $command = null,
    ) {
        $this->type = ConsoleCommandInputType::make($type);
    }

    public static function make(
        SymfonyInputArgument|SymfonyInputOption $input,
        ?int $pos = null,
        ?string $command = null,
    ): self
    {
        $isArg = is_a($input, SymfonyInputArgument::class);
        $ref = new \ReflectionObject($input);
        $args = [
            'type' => (is_a($input, SymfonyInputArgument::class) ? ConsoleCommandInputType::ARGUMENT : ConsoleCommandInputType::OPTION),
            'name' => $input->getName(),
            'mode' => value($ref->getProperty('mode')->getValue($input)),
            'description' => $input->getDescription(),
            'default' => $input->getDefault(),
            'shortcut' => $isArg ? null : $input->getShortcut(),
            'suggestedValues' => value($ref->getProperty('suggestedValues')->getValue($input)),
            'pos' => $pos,
            'command' => $command,
        ];
        return new self(...$args);
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

    public function isNone(): bool
    {
        return flags_has($this->mode, $this->type->getNoneFlag());
    }

    public function isNegatable(): bool
    {
        return flags_has($this->mode, $this->type->getNegatableFlag());
    }
}
