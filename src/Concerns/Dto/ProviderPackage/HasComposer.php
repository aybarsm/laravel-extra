<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;

trait HasComposer
{
    protected bool $hasComposer = false;

    public function hasComposer(): bool
    {
        return $this->hasComposer;
    }

    public function getComposerName(): string
    {
        return "{$this->author}/{$this->name}";
    }

    public function getComposerPath(): ?string
    {
        return $this->hasComposer() ? $this->getBasePath('composer.json') : null;
    }

    public function setHasComposerUsing(\Closure $callback): static
    {
        return $this->setHasComposer($callback($this));
    }
    public function setHasComposer(bool $hasComposer = true): static
    {
        $this->requireBasePathIf(
            $hasComposer,
            'Base path need to be set to enable composer.'
        );

        $composerPath = $this->getBasePath('composer.json');
        throw_if(
            !file_exists($composerPath),
            ProviderPackageException::class,
            sprintf('Composer file path `%s` does not exist', $composerPath)
        );

        $composerContent = file_get_contents($composerPath);
        throw_if(
            !is_string($composerContent) || !json_validate($composerContent),
            ProviderPackageException::class,
            sprintf('Composer file path `%s` does not have a valid JSON content', $composerPath)
        );

        $composer = json_decode($composerContent, true);
        throw_if(
            ($composer['name'] ?? null) !== $this->getComposerName(),
            ProviderPackageException::class,
            sprintf(
                'Composer package name does not match with `%s` package name of `%s`',
                ($composer['name'] ?? 'null'),
                $this->getComposerName()
            )
        );

        $this->hasComposer = $hasComposer;
        return $this;
    }
}
