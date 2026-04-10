<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Laravel\Extra\Enums\ArtisanCommandHas;
use Aybarsm\Laravel\Extra\Support\Fluent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;
final class ArtisanCommand extends Data
{
    public readonly Collection $aliases;
    public readonly Collection $arguments;
    public readonly Collection $options;
    protected Fluent $data;
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $class,
        iterable|Arrayable $aliases,
        #[DataCollectionOf(namespace\ArtisanCommandInput::class)]
        Collection $inputs,
    ) {
        $this->aliases = collect($aliases);
        $this->arguments = $inputs->where('type', ArtisanCommandInput::ARGUMENT);
        $this->data = new Fluent();
    }

    public static function normalizers(): array
    {
        return [namespace\Normalizers\ArtisanCommandNormalizer::class];
    }

    public function has(
        ArtisanCommandHas|string|array $of,
        ModeMatch|string $match = ModeMatch::ANY,
    ): bool
    {
        /** @var ArtisanCommandHas[] $of */
        $of = ArtisanCommandHas::makeAll(false, true, true, ...array_wrap($of));
        $match = ModeMatch::make($match, false);
        return $match->matchesBy(
            of: $of,
            callback: function(ArtisanCommandHas $item) {
                $dataKey = data_key($item->name, 'has');
                if ($this->data->has($dataKey)) return $this->data->get($dataKey);
                $ret = match($item) {
                    ArtisanCommandHas::ALIAS => $this->aliases->isNotEmpty(),
                    ArtisanCommandHas::ARGUMENT => $this->inputs->where,
                };
            }
        );
    }

//    public function hasArguments(): bool
//    {
//        return $this->arguments->count() > 0;
//    }
//
//    public function hasOptions(): bool
//    {
//        return $this->options->count() > 0;
//    }
}
