<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Dto\ConsoleCommandInput;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Console\Command as LaravelCommand;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandContract;
/**
 * @param array<int, string> $aliases
 * @param array<string, ConsoleCommandInput> $arguments
 * @param array<string, ConsoleCommandInput> $options
 */
final class ConsoleCommand implements ConsoleCommandContract
{
    public function __construct(
        public readonly string $class,
        public readonly string $name,
        public readonly string $description,
        public readonly array $aliases,
        public readonly array $arguments,
        public readonly array $options,
    ){
    }

    public static function make(SymfonyCommand|LaravelCommand $command): static
    {
        $meta = ['argumentsPos' => 0, 'optionsPos' => 0];
        $args = [
            'name' => $command->getName(),
            'class' => get_class($command),
            'description' => $command->getDescription(),
            'aliases' => $command->getAliases(),
            'arguments' => [],
            'options' => [],
        ];

        $def = $command->getDefinition();
        foreach(['arguments' => $def->getArguments(), 'options' => $def->getOptions()] as $type => $inputs) {
            $posKey = "{$type}Pos";
            foreach($inputs as $name => $input) {
                $args[$type][$name] = ConsoleCommandInput::make($input, $meta[$posKey], $args['class']);
                $meta[$posKey]++;
            }
        }

        return new static(...$args);
    }
}
