<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Illuminate\Support\Collection;
use Aybarsm\Laravel\Extra\Dto\ArtisanCommand;
final class ArtisanCommands extends Collection
{
    /**
     * @var array<string, ArtisanCommand>
     */
    protected $items = [];
}
