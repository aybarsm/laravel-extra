<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

trait HasAssets
{
    protected bool $hasAssets = false;

    public function hasAssets(): bool
    {
        return $this->hasAssets;
    }

    public function setHasAssets(bool $hasAssets = true): static
    {
        $this->requireBasePathIf(
            $hasAssets,
            'Base path need to be set to enable assets.'
        );

        $this->hasAssets = $hasAssets;

        return $this;
    }
}
