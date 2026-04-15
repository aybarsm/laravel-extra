<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

trait HasInternalConfig
{
    public array $internalConfig = [];
    public ?string $internalConfigFile = null;

    public function hasInternalConfigFile($configFilePath = null): static
    {
        $configFilePath ??= $this->shortName();

        if (! is_array($configFileName)) {
            $configFileName = [$configFileName];
        }

        $this->configFileNames = $configFileName;

        return $this;
    }
}
