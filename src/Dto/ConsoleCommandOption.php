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
        ?int $pos = null,
        ?string $command = null,
    ) {
        parent::__construct($type, $name, $mode, $description, $default, $suggestedValues);
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
