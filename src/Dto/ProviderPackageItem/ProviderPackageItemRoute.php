<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\ProviderPackageItem;

final class ProviderPackageItemRoute extends namespace\AbstractProviderPackageItem
{
    public function __construct(
        string|\Stringable $path,
        bool $isDiscovered = false,
    ){
        parent::__construct(
            path: $path,
            pathExtensions: 'php',
            isDiscovered: $isDiscovered,
        );
    }
}
