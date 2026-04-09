<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

use Aybarsm\Laravel\Extra\Support\Reflector;
use Illuminate\Support\Collection;

trait IsServiceProvider
{
    private static function getProviderMethods(): Collection
    {
        $ret = new Collection();
        $methods = Reflector::getMethods(static::class);
        if (!$methods) return $ret;

        foreach ($methods as $method) {
            preg_match('/(?P<section>[a-z_]+)(?P<method>[A-Z].*)/', $method->getName(), $matches);
            $section = $matches['section'] ?? null;
            $methodName = $matches['method'] ?? null;
            if (blank($section) || blank($methodName) || !in_array($section, ['boot', 'register', 'filament'])) continue;
            if (!array_key_exists($section, $ret)) $ret[$section] = [];
            $ret[$section][] = $methodName;
        }

//        if (method_exists(static::class, 'registerConfig')) $this->registerConfig();
//        if (method_exists($this, 'registerBindings')) $this->registerBindings();
//
//        $ret = [
//            'register' => [],
//            'boot' => [],
//            'filamentPanel' => [],
//        ];
//
//        foreach(Reflector::getMethods(static::class) as $method) {
//            if ($method->getDeclaringClass()->getName() !== static::class || in_array($method->getName(), ['register', 'boot', 'registerConfig', 'registerBindings'], true)) {
//                continue;
//            }
//            if (Str::startsWith($method->getName(), 'register')) {
//                $ret['register'][] = $method->getName();
//            }elseif (Str::startsWith($method->getName(), 'boot')) {
//                $ret['boot'][] = $method->getName();
//            }elseif (Str::isMatch('/^filament([a-zA-Z]+)Panel$/', $method->getName())) {
//                $ret['filamentPanel'][] = $method->getName();
//            }
//
//        }
//
//        return $ret;
    }
}
