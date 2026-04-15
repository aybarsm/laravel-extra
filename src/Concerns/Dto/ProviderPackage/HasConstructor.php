<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Custom\Spatie\LaravelPackageTools\Concerns\Package\DiscoveredValue;
use Aybarsm\Laravel\Extra\Custom\Spatie\LaravelPackageTools\Concerns\Package\ModeType;

trait HasConstructor
{
    public function __construct(string|object $provider){
        $value = new DiscoveredValue($provider);

        throw_if(
            $value->convertable(ModeType::CLASS_EXISTS),
            LaravelExtraException::class,
            sprintf('Provider class `%s` does not exist.', $provider)
        );

        $value->convert(ModeType::CLASS_EXISTS);

        throw_if(
            !$value->isSubOf(namespace\PackageServiceProvider::class),
            LaravelExtraException::class,
            sprintf(
                'Provider class `%s` must be a subclass of `%s`.',
                $provider,
                namespace\PackageServiceProvider::class
            )
        );

        $this->providerPath = new \ReflectionClass($value->getValue())->getFileName();
        $this->providerDir = dirname($this->providerPath);
        $this->tryResolvingComposerJsonPath();
    }
}
