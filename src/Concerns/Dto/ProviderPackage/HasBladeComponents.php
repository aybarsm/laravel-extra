<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage;

use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;

trait HasBladeComponents
{
    protected array $viewComponents = [];

    public function hasViewComponents(): bool
    {
        return count(array_keys($this->viewComponents)) > 0;
    }

    public function getViewComponents(): ?array
    {
        return $this->hasViewComponents() ? $this->viewComponents : null;
    }

    public function addViewComponents(
        string|\Stringable $prefix,
        string|\Stringable ...$names
    ): static
    {
        $this->requireBasePathIf(
            true,
            'Base path need to be set to add view components.'
        );

        $prefix = (string) $prefix;
        throw_if(
            blank($names),
            ProviderPackageException::class,
            sprintf(
                'No view components have been provided for `%s` prefix to add `%s`',
                $prefix,
                static::class
            )
        );

        foreach($names as $name) {
            $name = (string) $name;
            throw_if(
                blank($name),
                ProviderPackageException::class,
                sprintf(
                    'View component name cannot be blank for `%s` prefix to add `%s`',
                    $prefix,
                    static::class
                )
            );
            $this->viewComponents[$name] = $prefix;
        }

        return $this;
    }

    public function addViewComponent(
        string|\Stringable $prefix,
        string|\Stringable $name
    ): static
    {
        return $this->addViewComponents($prefix, $name);
    }
}
