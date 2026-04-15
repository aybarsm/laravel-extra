<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Dto\ProviderPackage\Items;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Support\Arr;
use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;

trait HasItemEssentials
{
    public readonly string $class;
    public readonly string $path;
    public readonly string $key;
    public readonly array $publishGroups;
    public readonly string $publishBasePath;
    public readonly bool $isDiscovered;

    public function hasClass(): bool
    {
        return isset($this->class) && filled($this->class);
    }
    public function hasPath(): bool
    {
        return isset($this->path) && filled($this->path);
    }
    public function hasKey(): bool
    {
        return isset($this->key) && filled($this->key);
    }

    public function hasPublishGroups(): bool
    {
        return isset($this->publishGroups) && filled($this->publishGroups);
    }

    public function hasPublishBasePath(): bool
    {
        return isset($this->publishBasePath) && filled($this->publishBasePath);
    }

    public function isDiscoverable(): bool
    {
        return isset($this->isDiscovered);
    }

    public function isPublishable(): bool
    {
        return $this->hasPath() && $this->hasPublishGroups() && $this->hasPublishBasePath();
    }

    public function getPublishPath(): ?string
    {
        return $this->isPublishable() ? fs_path($this->publishBasePath, basename($this->path)) : null;
    }

    protected static function getItemNameInfo(): string
    {
        return str(__CLASS__)
            ->afterLast('\\')
            ->after('Provider')
            ->headline()
            ->trim()
            ->value();
    }

    protected static function validateFilePath(
        string|\Stringable $value,
        string|\Stringable|array $extensions = [],
    ): string
    {
        $for = static::getItemNameInfo();

        throw_if(
            blank($value),
            ProviderPackageException::class,
            sprintf('%s path cannot be blank.', $for)
        );

        throw_if(
            !file_exists($value),
            ProviderPackageException::class,
            sprintf('%s file does not exists at `%s`', $for, $value)
        );

        $value = realpath((string) $value);

        if (blank($extensions)) return $value;
        $basename = basename($value);
        $extensions = array_unique(array_map(
            static fn ($ext) => str($ext)->trim()->start('.')->value(),
            array_wrap($extensions)
        ));
        $hasExtension = ModeMatch::ANY->matchesBy(
            $extensions,
            fn (string $ext) => str_ends_with($basename, $ext)
        );

        throw_if(
            !$hasExtension,
            ProviderPackageException::class,
            sprintf(
                '%s file `%s` must have `%s` extension',
                $for,
                $value,
                Arr::join($extensions, ', ', ' or ')
            )
        );

        return $value;
    }

    protected static function asClassString(string|object $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return is_subclass_of($value, \Stringable::class) ? (string) $value : get_class($value);
    }

    protected static function validateClass(
        string|object $value,
        string|object|array $isA = [],
        string|object|array $isSubOf = [],
    ): string
    {
        $for = static::getItemNameInfo();
        $value = static::asClassString($value);

        throw_if(
            !class_exists($value),
            ProviderPackageException::class,
            sprintf('%s class `%s` does not exist.', $for, $value)
        );

        $isA = array_unique(array_map(
            static fn ($item) => static::asClassString($item),
            array_wrap($isA)
        ));

        $isSubOf = array_unique(array_map(
            static fn ($item) => static::asClassString($item),
            array_wrap($isSubOf)
        ));

        if (blank($isA) && blank($isSubOf)) return $value;

        $validatesIsA = true;
        $validatesIsSubOf = true;

        if (filled($isA)){
            $validatesIsA = ModeMatch::ANY->matchesBy(
                $isA,
                fn (string $item) => is_a($value, $item, true)
            );
        }

        if (filled($isSubOf)){
            $validatesIsSubOf = ModeMatch::ANY->matchesBy(
                $isSubOf,
                fn (string $item) => is_subclass_of($value, $item)
            );
        }

        throw_if(
            !$validatesIsA && !$validatesIsSubOf,
            ProviderPackageException::class,
            sprintf(
                '%s class `%s` must be%s%s%s',
                $for,
                $value,
                (filled($isA) ? sprintf(' an instance of `%s`', Arr::join($isA, ', ', ' or ')) : ''),
                (filled($isA) && filled($isSubOf) ? ' or ' : ''),
                (filled($isSubOf) ? sprintf(' a subclass of `%s`', Arr::join($isSubOf, ', ', ' or ')) : ''),
            )
        );

        return $value;
    }
}
