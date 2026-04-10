<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Enums;

use Aybarsm\Extra\Support\Concerns\HasEnumHelpers;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
enum ArtisanCommandInputType
{
    use HasEnumHelpers;
    case ARGUMENT;
    case OPTION;

    public function isArgument(): bool
    {
        return $this === self::ARGUMENT;
    }

    public function isOption(): bool
    {
        return $this === self::OPTION;
    }

    public function getSymfonyInputClass(): string
    {
        return $this->isArgument() ? InputArgument::class : InputOption::class;
    }

    public function getRequiredFlag(): int
    {
        return $this->isArgument() ? InputArgument::REQUIRED : InputOption::VALUE_REQUIRED;
    }

    public function getOptionalFlag(): int
    {
        return $this->isArgument() ? InputArgument::OPTIONAL : InputOption::VALUE_OPTIONAL;
    }

    public function getArrayFlag(): int
    {
        return $this->isArgument() ? InputArgument::IS_ARRAY : InputOption::VALUE_IS_ARRAY;
    }

    protected function requireOption(string $for): void
    {
        throw_if(
            ! $this->isOption(),
            \BadMethodCallException::class,
            sprintf('%s is only applicable for input option.', trim($for))
        );
    }

    public function getNoneFlag(): int
    {
        $this->requireOption('None flag');
        return InputOption::VALUE_NONE;
    }

    public function getNegatableFlag(): int
    {
        $this->requireOption('Negatable flag');
        return InputOption::VALUE_NEGATABLE;
    }
}
