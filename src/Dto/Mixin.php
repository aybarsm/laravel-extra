<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Concerns\HasFluentMetaData;
use Aybarsm\Laravel\Extra\Exceptions\LaravelExtraException;

final class Mixin
{
    use HasFluentMetaData;

    public readonly string $mixin;
    public readonly string $bind;
    public readonly bool $replace;
    public readonly bool $isAnonymous;
    public readonly string $path;
    public function __construct(
        string|object $mixin,
        string|object|null $bind = null,
        ?bool $replace = null,
    ){
        $this->validateBind();
    }

    private static function prepare(
        string|object $mixin,
        string|object|null $bind = null,
        ?bool $replace = null,
    ): array
    {
        if (is_object($mixin) && is_subclass_of($mixin, \Stringable::class)) {
            $mixin = (string) $mixin;
        }
        if (is_object($bind) && is_subclass_of($bind, \Stringable::class)) {
            $bind = (string) $bind;
        }
    }

    private function validateBind(): void
    {
        $refBind = new \ReflectionClass($this->bind);
        $method = $refBind->hasMethod('mixin') ? $refBind->getMethod('mixin') : null;
        throw_if(
            ! $method || ! $method->isStatic() || ! $method->isPublic(),
            LaravelExtraException::class,
            sprintf(
                'Mixin class [%s] defined binding class [%s] does not have a valid mixin method.',
                $this->mixin,
                $this->bind
            )
        );
    }

    public static function make(
        string|object $mixin,
        string|object|null $bind = null,
        ?bool $replace = null,
    ): self
    {
        if (is_object($mixin) && is_subclass_of($mixin, \Stringable::class)) {
            $mixin = (string) $mixin;
        }
        if (is_object($bind) && is_subclass_of($bind, \Stringable::class)) {
            $bind = (string) $bind;
        }
    }
}
