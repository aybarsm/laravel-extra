<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Concerns\HasFluentData;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandCollectionContract;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandContract;
use Illuminate\Support\Collection;
final class ConsoleCommandCollection extends Collection implements ConsoleCommandCollectionContract
{
    use HasFluentData;
    /**
     * The items contained in the collection.
     *
     * @var array<string, ConsoleCommandContract>
     */
    protected $items = [];

    public function find(string $nameOrAlias): ?ConsoleCommandContract
    {
        $commandName = $this->getMapping()[$nameOrAlias] ?? null;
        if ($commandName === null) return null;
        return $this->get($commandName);
    }
    protected function getMapping(): array
    {
        return self::getData()->getOrSet(
            'mapping',
            fn () => $this->reduce(
                static fn(array $mapping, ConsoleCommand $command) => array_merge($mapping, $command->getNameMapping()),
                [],
            )
        );
    }
}
