<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Support\Fluent;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Console\Command as LaravelCommand;
final class ConsoleCommand
{
    private static Fluent $data;

    public readonly string $name;
    public readonly string $description;
    public readonly string $class;
    public readonly array $aliases;
    public readonly array $arguments;
    public readonly array $options;
    public function __construct(SymfonyCommand|LaravelCommand $command) {
        $this->name = $command->getName();
        $this->description = $command->getDescription();
        $this->class = get_class($command);
        $this->aliases = $command->getAliases();
    }
    private static function buildArguments(): array
    {

    }
    private static function data(): Fluent
    {
        if (!isset(self::$data)) self::$data = new Fluent();
        return self::$data;
    }


}
