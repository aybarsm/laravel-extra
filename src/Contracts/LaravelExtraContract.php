<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Contracts;

interface LaravelExtraContract
{
    public function getCache(): ?array;
    public static function registerMixin(
        string|object $mixin,
        string|object|null $bind = null,
        ?bool $replace = null,
    ): void;
}
