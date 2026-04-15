<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Support;

abstract class PackageServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    protected function getPackageBaseDir(): string
    {
        $ref = new \ReflectionClass(get_class($this));
        $path = $ref->getFileName();

        $packageBaseDir = dirname($reflector->getFileName());

        // Some packages like to keep Laravels directory structure and place
        // the service providers in a Providers folder.
        // move up a level when this is the case.
        if (str_ends_with($packageBaseDir, DIRECTORY_SEPARATOR.'Providers')) {
            $packageBaseDir = dirname($packageBaseDir);
        }

        return $packageBaseDir;
    }
}
