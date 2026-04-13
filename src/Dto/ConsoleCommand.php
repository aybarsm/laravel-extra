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
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandCollectionContract;

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

    public static function make(
        ConsoleCommandContract|SymfonyCommand|LaravelCommand $command
    ): ConsoleCommandContract
    {
        if (is_a($command, ConsoleCommandContract::class)) return $command;

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
            $concrete = $type === 'arguments' ? namespace\ConsoleCommandArgument::class : namespace\ConsoleCommandOption::class;
            $posKey = "{$type}Pos";
            foreach($inputs as $name => $input) {
                $args[$type][$name] = $concrete::make($input, $meta[$posKey], $args['class']);
                $meta[$posKey]++;
            }
        }

        return new self(...$args);
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

    public function getNameMapping(): array
    {
        return self::getData()->hasOr(
            self::getDataKey('mapping'),
            function (){
                $ret = [$this->getName() => $this->getName()];

                foreach($this->getAliases() as $alias) {
                    $ret[$alias] = $this->getName();
                }

                return $ret;
            }
        );
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

    public function hasAlias(): bool
    {
        return $this->has(ConsoleCommandHas::ALIAS);
    }
    public function hasArgument(): bool
    {
        return $this->has(ConsoleCommandHas::ARGUMENT);
    }

    public function hasArgumentRequired(): bool
    {
        return $this->has(ConsoleCommandHas::ARGUMENT_REQUIRED);
    }
    public function hasArgumentOptional(): bool
    {
        return $this->has(ConsoleCommandHas::ARGUMENT_OPTIONAL);
    }
    public function hasArgumentArray(): bool
    {
        return $this->has(ConsoleCommandHas::ARGUMENT_ARRAY);
    }
    public function hasOption(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION);
    }
    public function hasOptionRequired(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION_REQUIRED);
    }
    public function hasOptionOptional(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION_OPTIONAL);
    }
    public function hasOptionArray(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION_ARRAY);
    }
    public function hasOptionNone(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION_NONE);
    }
    public function hasOptionNegatable(): bool
    {
        return $this->has(ConsoleCommandHas::OPTION_NEGATABLE);
    }

    public function hasInputRequired(): bool
    {
        return $this->hasArgumentRequired() || $this->hasOptionRequired();
    }
    public function hasInputOptional(): bool
    {
        return $this->hasArgumentOptional() || $this->hasOptionOptional();
    }
    public function hasInputArray(): bool
    {
        return $this->hasArgumentArray() || $this->hasOptionArray();
    }
}
