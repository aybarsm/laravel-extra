<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;

trait HasBasePath
{
    protected readonly string $basePath;
    public function hasBasePath(): bool
    {
        return isset($this->basePath);
    }

    public function getBasePath(string|\Stringable ...$paths): ?string
    {
        return $this->hasBasePath() ? fs_path($this->basePath, ...$paths) : null;
    }

    public function setBasePathUsing(\Closure $callback): static
    {
        return $this->setBasePath($callback($this));
    }
    public function setBasePath(string|\Stringable $path): static
    {
        if ($this->hasBasePath()){
            throw new ProviderPackageException(
                sprintf('Base path has already been set to `%s`', $this->basePath)
            );
        }

        $path = (string) $path;

        throw_if(
            !file_exists($path) || !is_dir($path),
            ProviderPackageException::class,
            sprintf('Base path `%s` must be an existing directory.', $path)
        );

        $this->basePath = realpath($path);
        return $this;
    }
}
