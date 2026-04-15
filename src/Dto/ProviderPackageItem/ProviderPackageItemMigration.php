<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\ProviderPackageItem;

final class ProviderPackageItemMigration extends namespace\AbstractProviderPackageItem
{
    public function __construct(
        string|\Stringable $path,
        array $publishGroups = [],
        bool $isDiscovered = false,
    ){
        parent::__construct(
            path: $path,
            pathExtensions: 'php',
            publishGroups: $publishGroups,
            isDiscovered: $isDiscovered
        );
    }
}
