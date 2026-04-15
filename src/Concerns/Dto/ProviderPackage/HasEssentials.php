<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;
use Illuminate\Support\Str;

trait HasEssentials
{
    protected readonly string $author;
    protected readonly string $name;
    protected readonly string $version;

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasVersion(): bool
    {
        return isset($this->version);
    }

    public function getVersion(): ?string
    {
        return $this->hasVersion() ? $this->version : null;
    }
    public function getShortName(): string
    {
        return Str::after($this->name, 'laravel-');
    }

    public function setVersionUsing(\Closure $callback): static
    {
        return $this->setVersion($callback($this));
    }
    public function setVersion(string $version): static
    {
        if ($this->hasVersion()) {
            throw new ProviderPackageException(
                sprintf('Version has already been set to `%s`', $this->getVersion()),
            );
        }

        throw_if(
            blank($version),
            ProviderPackageException::class,
            'Version cannot be blank.',
        );

        $this->version = $version;
        return $this;
    }
}
