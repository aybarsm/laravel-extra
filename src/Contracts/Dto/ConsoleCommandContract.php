<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Illuminate\Console\Command as LaravelCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

interface ConsoleCommandContract
{
    public static function make(SymfonyCommand|LaravelCommand $command): static;
    public function isRequired(): bool;

}
