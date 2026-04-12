<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Laravel\Extra\Concerns\HasFluentData;
use Aybarsm\Laravel\Extra\Dto\AbstractConsoleCommandInput;
use Aybarsm\Laravel\Extra\Enums\ConsoleCommandHas;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Console\Command as LaravelCommand;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandContract;
/**
 * @param array<int, string> $aliases
 * @param array<string, AbstractConsoleCommandInput> $arguments
 * @param array<string, AbstractConsoleCommandInput> $options
 */
final class ConsoleCommand implements ConsoleCommandContract
{
    use HasFluentData;
    public function __construct(
        public readonly string $class,
        public readonly string $name,
        public readonly string $description,
        public readonly array $aliases,
        public readonly array $arguments,
        public readonly array $options,
    ){
    }

    public static function make(SymfonyCommand|LaravelCommand $command): ConsoleCommandContract
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
                $args[$type][$name] = AbstractConsoleCommandInput::make($input, $meta[$posKey], $args['class']);
                $meta[$posKey]++;
            }
        }

        return app()->makeWith(ConsoleCommandContract::class, $args);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
    public function hasAny(ConsoleCommandHas|string ...$of): bool
    {
        return ModeMatch::ANY->matchesBy(
            ConsoleCommandHas::makeAll(false, true, true, ...$of),
            fn (ConsoleCommandHas $item) => $this->has($item)
        );
    }

    public function hasAll(ConsoleCommandHas|string ...$of): bool
    {
        return ModeMatch::ALL->matchesBy(
            ConsoleCommandHas::makeAll(false, true, true, ...$of),
            fn (ConsoleCommandHas $item) => $this->has($item)
        );
    }

    public function has(ConsoleCommandHas|string $of): bool
    {
        $of = ConsoleCommandHas::make($of, false);
        return self::getData()->hasOr(
            self::getDataKey("has.{$of->name}"),
            fn () => $of->has($this)
        );
    }
}
