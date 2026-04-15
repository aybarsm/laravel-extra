<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

trait HasThrowIf
{
    private static function throwIf(
        mixed $condition,
        mixed $parameters,
        mixed $exceptionClass = null,
    ): void
    {
        $condition = value($condition);
        $parameters = array_wrap(value($parameters, $condition));
        $exceptionClass = value($exceptionClass);
//        $exceptionClass = call_until(
//            static fn (mixed $result) => filled($result) && is_string($result) && is_subclass_of($result. \Exception::class, $exceptionClass, true),
//            $exceptionClass,
//            static function () {
//                try{
//                    return self::
//                }
//            },
//        );

        throw_if(
            $condition,
            is_null($exceptionClass) ? \RuntimeException::class : $exceptionClass,
            ...$parameters,
        );
    }
}
