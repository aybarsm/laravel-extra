<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Enums;

use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;
use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandContract;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandArgumentContract as CommandArgument;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandOptionContract as CommandOption;

enum ConsoleCommandHas implements HasEnumHelpersContract
{
    use HasEnumHelpers;

    case ALIAS;
    case ARGUMENT;
    case ARGUMENT_REQUIRED;
    case ARGUMENT_OPTIONAL;
    case ARGUMENT_ARRAY;
    case OPTION;
    case OPTION_REQUIRED;
    case OPTION_OPTIONAL;
    case OPTION_ARRAY;
    case OPTION_NEGATABLE;
    case OPTION_NONE;

    public final const ConsoleCommandHas ALIASES = self::ALIAS;
    public final const ConsoleCommandHas ARGUMENTS = self::ARGUMENT;
    public final const ConsoleCommandHas OPTIONS = self::OPTION;

    public function has(ConsoleCommandContract $dto): bool
    {
        return match($this){
            self::ALIAS => count($dto->getAliases()) > 0,
            self::ARGUMENT => count($dto->getArguments()) > 0,
            self::OPTION => count($dto->getOptions()) > 0,
            default => value(function () use ($dto) {
                $of = str($this->name);
                $itemsUsing = $of->before('_')->plural()->title()->start('get')->value();
                $isUsing = $of->afterLast('_')->title()->start('is')->value();
                return array_find_key(
                    $dto->{$itemsUsing}(),
                    static fn (CommandArgument|CommandOption $item) => $item->{$isUsing}(),
                ) !== null;
            })
        };
    }
}
