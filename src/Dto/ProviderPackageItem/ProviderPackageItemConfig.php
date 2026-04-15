<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\ProviderPackageItem;

final class ProviderPackageItemConfig extends namespace\AbstractProviderPackageItem
{
    public function __construct(
        string|\Stringable $path,
        string|\Stringable $key,
        array $publishGroups = [],
        bool $isDiscovered = false,
    ){
        parent::__construct(
            path: $path,
            pathExtensions: 'php',
            key: $key,
            publishGroups: $publishGroups,
            publishBasePath: config_path(),
            isDiscovered: $isDiscovered,
        );
    }
}
