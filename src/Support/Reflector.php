<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Support;

final class Reflector extends \Illuminate\Support\Reflector
{
    public static function class(string $item): ?\ReflectionClass
    {
        if (blank($item) || (!class_exists($item) && !interface_exists($item))) return null;
        return  new \ReflectionClass($item);
    }

    public static function object(object $item): ?\ReflectionClass
    {
        return new \ReflectionObject($item);
    }

    public static function reflect(object|string $item): null|\ReflectionClass|\ReflectionObject
    {
        if (is_object($item)) return self::object($item);
        return self::class($item);
    }

    /**
     * Get the specified class or objects methods.
     *
     *
     * @param  object|string  $item
     * @param  bool  $onlySelf
     * @return \ReflectionMethod[]|null
     */
    public static function getMethods(object|string $item, bool $onlySelf = true): ?array
    {
        $ref = self::reflect($item);
        if ($ref === null) return null;
        $ret = [];

        foreach($ref->getMethods() as $method) {
            $isSelf = $method->getDeclaringClass()->getName() === $ref->getName();
            if (!$isSelf && $onlySelf) continue;
            $ret[] = $method;
        }

        return $ret;
    }
}
