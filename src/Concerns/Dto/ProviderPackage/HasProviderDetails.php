<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;
use Illuminate\Support\ServiceProvider;

trait HasProviderDetails
{
    protected readonly object $providerDetails;
    public function hasProviderDetails(): bool
    {
        return isset($this->providerDetails);
    }

    public function getProviderDetails(): ?object
    {
        return $this->providerDetails ?? null;
    }

    public function getProviderClass(): ?string
    {
        return $this->getProviderDetails()?->class ?? null;
    }

    public function getProviderFilePath(): ?string
    {
        return $this->getProviderDetails()?->pathFile ?? null;
    }

    public function getProviderDirectoryPath(): ?string
    {
        return $this->getProviderDetails()?->pathDirectory ?? null;
    }

    public function setProviderUsing(\Closure $callback): static
    {
        return $this->setProvider($callback($this));
    }

    public function setProvider(string|object $provider): static
    {
        if ($this->hasProviderDetails()){
            throw new ProviderPackageException(
                sprintf('Provider has already been set to `%s`', $this->providerDetails['class'])
            );
        }

        $provider = is_object($provider) ? get_class($provider) : $provider;

        throw_if(
            !class_exists($provider),
            ProviderPackageException::class,
            sprintf('Provider class `%s` does not exist.', $provider)
        );

        throw_if(
            !is_subclass_of($provider, ServiceProvider::class),
            ProviderPackageException::class,
            sprintf(
                'Provider class `%s` must be a subclass of `%s`.',
                $provider,
                ServiceProvider::class
            )
        );

        $this->providerDetails = (object)[
            'class' => $provider,
            'pathFile' => ($providerPath = new \ReflectionClass($provider)->getFileName()),
            'pathDirectory' => dirname($providerPath),
        ];

        return $this;
    }
}
