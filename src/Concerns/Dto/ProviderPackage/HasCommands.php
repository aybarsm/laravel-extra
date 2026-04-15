<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;
use Aybarsm\Laravel\Extra\Dto\ProviderPackageItem\ProviderPackageItemCommand;

trait HasCommands
{
    /** @var array<string, ProviderPackageItemCommand> */
    protected array $commands = [];

    public function hasCommands(): bool
    {
        return count($this->commands) > 0;
    }
    public function getCommands(): ?array
    {
        return $this->hasCommands() ? $this->commands : null;
    }
    public function addCommand(
        string|object $command,
        bool $consoleOnly = false,
    ): static
    {
        $item = new ProviderPackageItemCommand($command, $consoleOnly);
        $this->commands[$item->class] = $item;
        return $this;
    }

    public function addCommands(
        bool $consoleOnly = false,
        string|object ...$commands
    ): static
    {
        foreach($commands as $command) {
            $this->addCommand($command, $consoleOnly);
        }
        return $this;
    }
}
