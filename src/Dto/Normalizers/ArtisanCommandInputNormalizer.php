<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto\Normalizers;

use Illuminate\Support\Arr;
use Spatie\LaravelData\Normalizers\Normalized\Normalized;
use Spatie\LaravelData\Normalizers\Normalizer;

use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;
use Aybarsm\Laravel\Extra\Enums\ArtisanCommandInputType;
final class ArtisanCommandInputNormalizer implements Normalizer
{
    public function normalize(mixed $value): null|array|Normalized
    {
        $objectClass = [
            SymfonyInputArgument::class,
            SymfonyInputOption::class,
        ];

        throw_if(
            ! is_object($value) || ! is_one_of($value, $objectClass),
            \InvalidArgumentException::class,
            sprintf(
                'Normalizer `%s` requires value to be `%s` object.',
                static::class,
                Arr::join(array_wrap($objectClass), ', ', ' or ')
            )
        );

        $isArg = is_a($value, SymfonyInputArgument::class);
        $ref = new \ReflectionObject($value);

        /** @var SymfonyInputArgument|SymfonyInputOption $value */
        return [
            'type' => ($isArg ? ArtisanCommandInputType::ARGUMENT : ArtisanCommandInputType::OPTION),
            'name' => $value->getName(),
            'mode' => value($ref->getProperty('mode')->getValue($value)),
            'description' => $value->getDescription(),
            'default' => $value->getDefault(),
            'shortcut' => $isArg ? null : $value->getShortcut(),
            'suggestedValues' => value($ref->getProperty('suggestedValues')->getValue($value)),
        ];
    }
}
