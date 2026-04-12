<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandOptionContract;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandInputType;

class ConsoleCommandOption extends namespace\AbstractConsoleCommandInput implements ConsoleCommandOptionContract
{
    public readonly ?string $shortcut;
    public function __construct(
        ConsoleCommandInputType|string $type,
        string $name,
        int $mode,
        ?string $shortcut = null,
        ?string $description = null,
        mixed $default = null,
        array $suggestedValues = [],
        ?int $position = null,
        ?string $commandClass = null,
    ) {
        parent::__construct($type, $name, $mode, $description, $default, $suggestedValues, $position, $commandClass);
        $this->shortcut = $shortcut;
    }

    public function isNone(): bool
    {
        return flags_has($this->mode, $this->type->getNoneFlag());
    }

    public function isNegatable(): bool
    {
        return flags_has($this->mode, $this->type->getNegatableFlag());
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function toArray(): array
    {
        $ret = parent::toArray();
        $ret['shortcut'] = $this->shortcut;
        return $ret;
    }
}
