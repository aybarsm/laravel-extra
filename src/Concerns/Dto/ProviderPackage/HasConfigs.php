<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Dto\ProviderPackageItem\ProviderPackageItemConfig;

trait HasConfigs
{
    /** @var array<string, ProviderPackageItemConfig> */
    protected array $configs = [];
    protected string $configsDirectory = 'config';

    public function getConfigsDirectory(): string
    {
        return $this->configsDirectory;
    }

    public function setConfigsDirectory(
        string|\Stringable $configsDirectory,
    ): static
    {
        $this->requireBasePathIf(
            true,
            'Base path need to be set to set config files directory.'
        );

        $relPath = $this->getBasePath($configsDirectory);
        throw_if(
            !file_exists($relPath) || !is_dir($relPath),
            sprintf('Configs path `%s` must be an existing directory.', $relPath)
        );

        $this->configsDirectory = $configsDirectory;
        return $this;
    }

    public function hasConfigFiles(): bool
    {
        return count($this->configs) > 0;
    }

    public function getConfigFiles(): ?array
    {
        return $this->hasConfigFiles() ? $this->configs : null;
    }

    public function getConfigFilePath(
        string|\Stringable $fileName,
    ): ?string
    {
        return $this->hasBasePath() ? $this->getBasePath($this->getConfigsDirectory(), $fileName) : null;
    }

    public function addConfigFile(
        string|\Stringable $fileName,
        string|\Stringable|null $key = null,
        bool $publishable = true,
        array $publishGroups = [],
    ): static
    {
        $this->requireBasePathIf(
            true,
            'Base path need to be set to add config files.'
        );

        $key = is_null($key) ? $this->getShortName() : $key;
        $path = $this->getConfigFilePath($fileName);

        if ($publishable && blank($publishGroups)){
            $publishGroups = [
                str($this->getName())->lower()->finish('-config')->value(),
            ];
        }

        $item = new ProviderPackageItemConfig(
            path: $path,
            key: $key,
            publishGroups: $publishGroups
        );

        $this->configs[$item->path] = $item;

        return $this;
    }

    public function addConfigFiles(
        string|\Stringable|null $key = null,
        bool $publishable = true,
        array $publishGroups = [],
        string|\Stringable ...$paths,
    ): static
    {
        foreach ($paths as $path) {
            $this->addConfigFile($path, $key, $publishable, $publishGroups);
        }

        return $this;
    }
}
