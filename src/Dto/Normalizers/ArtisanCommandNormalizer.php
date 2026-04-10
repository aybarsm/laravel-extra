<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\Normalizers;

use Illuminate\Support\Arr;
use Spatie\LaravelData\Normalizers\Normalized\Normalized;
use Spatie\LaravelData\Normalizers\Normalizer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Console\Command as LaravelCommand;
final class ArtisanCommandNormalizer implements Normalizer
{
    public function normalize(mixed $value): null|array|Normalized
    {
        $objectClass = [
            SymfonyCommand::class,
            LaravelCommand::class,
        ];

        throw_if(
            ! is_object($value) || ! is_one_of($value, $objectClass),
            \InvalidArgumentException::class,
            sprintf(
                'Normalizer `%s` requires value to be `%s` object%s.',
                static::class,
                Arr::join(array_wrap($objectClass), ', ', ' or '),
                (is_object($value) ? sprintf(' `%s` given', get_class($value)) : ''),
            )
        );

        /** @var SymfonyCommand|LaravelCommand $value */
        $definition = $value->getDefinition();

        return [
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'class' => get_class($value),
            'aliases' => $value->getAliases(),
            'inputs' => array_merge($definition->getArguments(), $definition->getOptions()),
        ];
    }
}
