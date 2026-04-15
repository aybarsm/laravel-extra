<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts\Dto;

use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Support\Enumerable;

interface ConsoleCommandCollectionContract extends \ArrayAccess, CanBeEscapedWhenCastToString, Enumerable
{
    public function getAliasMappings(): array;
    public function getDescriptionMappings(): array;
    public function find(string $nameOrAlias): ?namespace\ConsoleCommandContract;
}
