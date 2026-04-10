<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Concerns;

use Aybarsm\Laravel\Extra\Dto\ArtisanCommands;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputDefinition;
trait BuildsArtisanCommandsDto
{
    private function buildArtisanCommandsDto(): ArtisanCommands
    {
        $ret = [
            'commands' => [],
            'mapping' => [],
        ];

        foreach(Artisan::all() as $command => $object){
            $definition = self::buildArtisanCommandMetaDefinition($object->getDefinition());
            $ret['commands'][$command] = [
                'name' => $object->getName(),
                'class' => get_class($object),
                'aliases' => Arr::mapWithKeys(
                    $object->getAliases(),
                    static fn ($alias) => [$alias => ['name' => $alias]],
                ),
                'arguments' => $definition['arguments'],
                'options' => $definition['options'],
            ];

            $ret['mapping'][$command] = $command;

            foreach($ret['commands'][$command]['aliases'] as $alias){
                $ret['mapping'][$alias['name']] = $command;
            }
        }

        return $ret;
    }

    private static function buildArtisanCommandDefinition(InputDefinition $definition): array
    {
        $ret = [
            'arguments' => [],
            'options' => [],
        ];

        foreach(['arguments' => $definition->getArguments(), 'options' => $definition->getOptions()] as $section => $items){
            $isArg = $section === 'arguments';
            foreach($items as $name => $item){
                /** @var \Symfony\Component\Console\Input\InputArgument|\Symfony\Component\Console\Input\InputOption $item */
                $ref = new \ReflectionObject($item);

                $mode = value($ref->getProperty('mode')->getValue($item));
                $suggested = $ref->getProperty('suggestedValues')->getValue($item);
                if (is_callable($suggested)){
                    $suggested = app()->call($suggested);
                }

                $ret[$section][$name] = [
                    'name' => $name,
                    'default' => $item->getDefault(),
                    'description' => $item->getDescription(),
                    'mode' => $mode,
                    'suggested' => $suggested,
                    'isRequired' => self::validate()::flagsHas($mode, ($isArg ? $item::REQUIRED : $item::VALUE_REQUIRED)),
                    'isOptional' => self::validate()::flagsHas($mode, ($isArg ? $item::OPTIONAL : $item::VALUE_OPTIONAL)),
                    'isArray' => self::validate()::flagsHas($mode, ($isArg ? $item::IS_ARRAY : $item::VALUE_IS_ARRAY)),
                ];

                if (!$isArg) {
                    $ret[$section][$name]['isNone'] = self::validate()::flagsHas($mode, $item::VALUE_NONE);
                    $ret[$section][$name]['isNegatable'] = self::validate()::flagsHas($mode, $item::VALUE_NEGATABLE);
                }
            }
        }

        return $ret;
    }
}
