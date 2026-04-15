<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Dto\ProviderPackageItem\ProviderPackageItemRoute;
use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;
use Symfony\Component\Finder\Finder;

trait HasRoutes
{
    /** @var array<string, ProviderPackageItemRoute> */
    protected array $routes = [];
    protected string $routesDirectory = 'routes';
    protected bool $discoversRoutes = false;

    public function hasRoutes(): bool
    {
        return count($this->routes) > 0;
    }

    public function getRoutes(): ?array
    {
        return $this->hasRoutes() ? $this->routes : null;
    }
    public function getRoutesDirectory(): string
    {
        return $this->routesDirectory;
    }

    public function discoversRoutes(): bool
    {
        return $this->discoversRoutes;
    }
    public function getRouteFilePath(
        string|\Stringable $fileName,
    ): ?string
    {
        return $this->hasBasePath() ? $this->getBasePath($this->getRoutesDirectory(), $fileName) : null;
    }

    protected function validateRoutesPath(
        mixed ...$parameters,
    ): void
    {
        $this->requireBasePathIf(true, ...$parameters,);

        $path = $this->getBasePath($this->getRoutesDirectory());
        throw_if(
            !file_exists($path) || !is_dir($path),
            ProviderPackageException::class,
            sprintf('Routes path `%s` must be an existing directory.', $path),
        );
    }

    public function setRoutesDirectory(
        string|\Stringable $routesDirectory,
    ): static
    {
        $this->routesDirectory = $routesDirectory;
        $this->validateRoutesPath(
            'Base path need to be set to set routes directory.'
        );

        return $this;
    }

    protected function addRouteInternally(
        string|\Stringable $fileName,
        bool $isDiscovered = false,
    ): static
    {
        $this->validateRoutesPath(
            'Base path need to be set to to add routes.'
        );

        $path = $this->getRouteFilePath($fileName);

        $item = new ProviderPackageItemRoute(
            path: $path,
            isDiscovered: $isDiscovered,
        );

        if (!array_key_exists($item->path, $this->routes)){
            $this->routes[$item->path] = $item;
        }

        return $this;
    }

    public function addRoute(string|\Stringable $fileName): static
    {
        return $this->addRouteInternally($fileName);
    }

    public function addRoutes(string|\Stringable ...$fileNames): static
    {
        foreach($fileNames as $fileName){
            $this->addRoute($fileName);
        }

        return $this;
    }

    public function setDiscoversRoutes(bool $enabled = true): static
    {
        if (!$enabled) {
            $this->routes = array_filter(
                $this->routes,
                static fn (ProviderPackageItemRoute $item) => !$item->isDiscovered
            );

            $this->discoversRoutes = false;
            return $this;
        }

        $this->validateRoutesPath(
            'Base path need to be set to enable discovers routes.'
        );

        $fileNames = Finder::create()
            ->in($this->getBasePath($this->getRoutesDirectory()))
            ->name('*.php')
            ->depth('== 0')
            ->files()
            ->sortByName();

        foreach(array_keys(iterator_to_array($fileNames)) as $fileName) {
            $this->addRouteInternally($fileName,  true);
        }

        $this->discoversRoutes = true;

        return $this;
    }
}
