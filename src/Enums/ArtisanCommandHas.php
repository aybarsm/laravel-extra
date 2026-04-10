<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Enums;

use Aybarsm\Extra\Support\Concerns\HasEnumHelpers;
use Aybarsm\Laravel\Extra\Dto\ArtisanCommand;

enum ArtisanCommandHas
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

//    public function has(ArtisanCommand $dto): bool
//    {
//        return match($this){
//            self::ALIAS => $dto->aliases->isNotEmpty(),
//            self::ARGUMENT => $dto->arguments->isNotEmpty(),
//            self::OPTION => $dto->options->isNotEmpty(),
//            self::ARGUMENT_REQUIRED => $dto->arguments->first(static fn ()) !== null,
//
//        };
//    }
}
