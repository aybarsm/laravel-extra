<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class ArtisanCommandDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        #[DataCollectionOf(namespace\ArtisanCommandAliasDto::class)]
        public readonly array $aliases,
        #[DataCollectionOf(namespace\ArtisanCommandArgumentDto::class)]
        public readonly array $arguments,
        #[DataCollectionOf(namespace\ArtisanCommandOptionDto::class)]
        public readonly array $options,
    ) {
    }

    public static function from(mixed ...$payloads): static
    {
        foreach ($payloads as $idx => $payload) {
            if (isset($payload['aliases'])) {
                $payloads[$idx]['aliases'] = array_map(
                    static fn ($alias) => is_string($alias) ? namespace\ArtisanCommandAliasDto::from($alias) : $alias,
                    $payload['aliases']
                );
            }
        }
        return parent::from(...$payloads);
    }
}
