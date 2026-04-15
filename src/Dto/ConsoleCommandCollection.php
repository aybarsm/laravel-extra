<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Concerns\HasFluentMetaData;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandCollectionContract;
use Aybarsm\Laravel\Extra\Contracts\Dto\ConsoleCommandContract;
use Illuminate\Support\Collection;
final class ConsoleCommandCollection extends Collection implements ConsoleCommandCollectionContract
{
    use HasFluentMetaData;
    /**
     * The items contained in the collection.
     *
     * @var array<string, ConsoleCommandContract>
     */
    protected $items = [];

    public function find(string $nameOrAlias): ?ConsoleCommandContract
    {
        $commandName = $this->getAliasMappings()[$nameOrAlias] ?? null;
        if ($commandName === null) return null;
        return $this->get($commandName);
    }
    public function getAliasMappings(): array
    {
        return self::getMetaData()->getOrSet(
            'aliasMappings',
            fn () => $this->reduce(
                static fn(array $mapping, ConsoleCommand $command) => array_merge($mapping, $command->getAliasMapping()),
                [],
            )
        );
    }

    public function getDescriptionMappings(): array
    {
        return self::getMetaData()->getOrSet(
            'descriptionMappings',
            fn () => $this->reduce(
                static fn(array $mapping, ConsoleCommand $command) => array_merge($mapping, [$command->getName() => $command->getDescription()]),
                [],
            )
        );
    }
}
