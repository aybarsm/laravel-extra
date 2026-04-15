<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\ProviderPackageItem;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Support\Arr;
use Aybarsm\Laravel\Extra\Exceptions\ProviderPackageException;

abstract class AbstractProviderPackageItem implements namespace\Contracts\ProviderPackageItemContract
{
    public readonly string $class;
    public readonly string $path;
    public readonly string $key;
    public readonly array $publishGroups;
    public readonly string $publishBasePath;
    public readonly bool $isDiscovered;

    public function __construct(
        string|object|null $class = null,
        string|object|array $classIsA = [],
        string|object|array $classIsSubOf = [],
        string|\Stringable|null $path = null,
        string|\Stringable|array $pathExtensions = [],
        string|\Stringable|null $key = null,
        ?array $publishGroups = null,
        string|\Stringable|null $publishBasePath = null,
        ?bool $isDiscovered = null,
    ){
        if (!is_null($class)){
            $this->class = static::validateClass($class, $classIsA, $classIsSubOf);
        }
        if (!is_null($path)){
            $this->path = static::validateFilePath($path, $pathExtensions);
        }
        if (!is_null($key)){
            $this->key = static::validateKey($key);
        }

        if (!blank($publishGroups)){
            $this->publishGroups = array_unique($publishGroups);
            throw_if(
                blank($publishBasePath),
                \LogicException::class,
                'Publish base path can not be blank when publish groups are provided.'
            );
            $this->publishBasePath = (string) $publishBasePath;
        }

        if (!is_null($isDiscovered)){
            $this->isDiscovered = $isDiscovered;
        }

    }

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

    public function isPublishable(): bool
    {
        return $this->hasPath() && $this->hasPublishGroups() && $this->hasPublishBasePath();
    }

    public function isDiscoverable(): bool
    {
        return isset($this->isDiscovered);
    }

    public function getClass(): ?string
    {
        return $this->hasClass() ? $this->class : null;
    }

    public function getPath(): ?string
    {
        return $this->hasPath() ? $this->path : null;
    }

    public function getKey(): ?string
    {
        return $this->hasKey() ? $this->key : null;
    }

    public function getPublishGroups(): ?array
    {
        return $this->hasPublishGroups() ? $this->publishGroups : null;
    }

    public function getPublishBasePath(): ?string
    {
        return $this->hasPublishBasePath() ? $this->publishBasePath : null;
    }

    public function getPublishPath(): ?string
    {
        return $this->isPublishable() ? fs_path($this->publishBasePath, basename($this->path)) : null;
    }

    public function getIsDiscovered(): ?bool
    {
        return $this->isDiscoverable() ? $this->isDiscovered : null;
    }

    protected static function getItemNameInfo(): string
    {
        return str(static::class)
            ->afterLast('\\')
            ->headline()
            ->chopStart('Provider')
            ->trim()
            ->chopStart('Package')
            ->trim()
            ->chopStart('Item')
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

        $validatesIsA = filled($isA) ? false : null;
        $validatesIsSubOf = filled($isSubOf) ? false : null;

        if (is_null($validatesIsA) && is_null($validatesIsSubOf)) return $value;

        if ($validatesIsA === false){
            $isA = array_unique(array_map(
                static fn ($item) => static::asClassString($item),
                array_wrap($isA)
            ));
            $validatesIsA = ModeMatch::ANY->matchesBy(
                $isA,
                fn (string $item) => is_a($value, $item, true)
            );
            if ($validatesIsA === true) return $value;
        }

        if ($validatesIsSubOf === false){
            $isSubOf = array_unique(array_map(
                static fn ($item) => static::asClassString($item),
                array_wrap($isSubOf)
            ));
            $validatesIsSubOf = ModeMatch::ANY->matchesBy(
                $isSubOf,
                fn (string $item) => is_subclass_of($value, $item)
            );
            if ($validatesIsSubOf === true) return $value;
        }

        $exceptionMessage = [$for, $value];
        $exceptionMessage[] = $validatesIsA === false ? sprintf(' an instance of `%s`', Arr::join($isA, ', ', ' or ')) : '';
        $exceptionMessage[] = !is_null($validatesIsA) && !is_null($validatesIsSubOf) ? ' or ' : '';
        $exceptionMessage[] = $validatesIsSubOf === false ? sprintf(' a subclass of `%s`', Arr::join($isSubOf, ', ', ' or ')) : '';

        throw new ProviderPackageException(sprintf('%s class `%s` must be%s%s%s', ...$exceptionMessage));
    }

    protected static function validateKey(
        string|\Stringable $value,
    ): string
    {
        $value = (string) $value;
        $for = static::getItemNameInfo();
        throw_if(
            blank($value),
            ProviderPackageException::class,
            sprintf('%s key cannot be blank.', $for)
        );
        return $value;
    }
}
