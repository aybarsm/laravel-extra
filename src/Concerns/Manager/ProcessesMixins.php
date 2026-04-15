<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns\Manager;

trait ProcessesMixins
{
    public static function registerMixin(
        string|object $mixin,
        string|object|null $bind = null,
        ?bool $replace = null,
    ): void
    {
        if (is_object($mixin) && is_subclass_of($mixin, \Stringable::class)) {
            $mixin = (string) $mixin;
        }
        if (is_object($bind) && is_subclass_of($bind, \Stringable::class)) {
            $bind = (string) $bind;
        }
    }
//    private static function resolveMixin(
//        string|object $mixin,
//        null|string|object $bind,
//        ?bool $replace
//    ):

    private static function validateMixin(
        string|object $mixin,
        null|string|object $bind,
        ?bool $replace
    ): array
    {
        self::throw_if(
            !class_exists($mixin),
            sprintf('Mixin class [%s] does not exist.', $mixin)
        );

        $refMixin = new \ReflectionClass($mixin);
        self::throw_if(
            !$refMixin->isInstantiable(),
            sprintf('Mixin class [%s] is not instantiable.', $mixin)
        );

        self::throw_if(
            is_null($bind) && !$refMixin->hasConstant('BIND'),
            sprintf('Mixin class [%s] does not have a binding class constant.', $mixin)
        );

        $bind = $refMixin->hasConstant('BIND') ? $refMixin->getConstant('BIND') : $bind;
        self::throw_if(
            !class_exists($bind),
            sprintf('Mixin class [%s] defined binding class [%s] does not exist.', $mixin, $bind)
        );

        /** @var \ReflectionClass $refBind */
        $refBind = self::getMetaData()->getOrSet(
            "mixin.bindings.{$bind}",
            static fn () => new \ReflectionClass($bind)
        );

        $method = $refBind->hasMethod('mixin') ? $refBind->getMethod('mixin') : null;
        self::throw_if(
            ! $method || ! $method->isStatic() || ! $method->isPublic(),
            sprintf('Mixin class [%s] defined binding class [%s] does not have a valid mixin method.', $mixin, $bind)
        );

        $replace = $refMixin->hasConstant('REPLACE') && $refMixin->getConstant('REPLACE') === true;
        return [$bind, $replace];
    }
    private function buildMixinDiscovery(): array
    {
        $ret = self::getMetaData()->get('mixins.discovered', []);

        foreach(self::config('mixins', []) as $keyOrMixin => $mixinOrBind){
            if (is_string($keyOrMixin) && class_exists($keyOrMixin)) {
                [$mixin, $bind] = [$keyOrMixin, $mixinOrBind];
            }else {
                [$mixin, $bind] = [$mixinOrBind, null];
            }

            [$bind, $replace] = self::validateMixin($mixin, $bind);
            $ret[$bind][$mixin] = $replace;
        }

        self::getMetaData()->forget('mixins.bindings');

        return $ret;
    }
    private function registerMixins(): void
    {
        if (self::getMetaData()->get('mixins.registered')){
            return;
        }
        debug_context([
            'stage' => 'LaravelExtra :: registerMixins',
            'data' => self::getMetaData()->toArray(),
            'ts' => now('UTC')->toIso8601ZuluString('microsecond')
        ]);

        foreach($this->buildMixinDiscovery() as $bind => $mixins){
            foreach($mixins as $mixin => $replace){
                $bind::mixin(app()->make($mixin), $replace);
            }
        }

        self::getMetaData()->set('mixins.registered', true);
    }

    public static function addMixin(string $mixin, ?string $bind): void
    {
        [$bind, $replace] = self::validateMixin($mixin, $bind);
        self::getMetaData()->set("mixins.discovered.{$bind}.{$mixin}", $replace);
    }


}
