<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;

use Aybarsm\Laravel\Extra\Enums\ArtisanCommandInputType;
use Aybarsm\Laravel\Extra\Support\Fluent;
use Spatie\LaravelData\Data;

final class ArtisanCommandInput extends Data
{
    private static Fluent $data;

    public readonly ArtisanCommandInputType $type;
    public readonly ?int $pos;
    public function __construct(
        ArtisanCommandInputType|string $type,
        public readonly string $name,
        public readonly int $mode,
        public readonly ?string $description = null,
        public readonly mixed $default = null,
        public readonly ?string $shortcut = null,
        public readonly array $suggestedValues = [],
        public readonly ?string $owner = null,
    ) {
        $this->type = ArtisanCommandInputType::make($type);
        $this->pos = $this->calculatePos();
    }

    private function calculatePos(): ?int
    {
        if (blank($this->owner)) return null;
        $posKey = "{$this->owner}.{$this->type->name}.{$this->name}.pos";
        if (self::data()->has($posKey)) return self::data()->get($posKey);
        $counterKey = "{$this->owner}.{$this->type->name}.counter";
        $pos = self::data()->increase($counterKey);
        self::data()->set($posKey, $pos);
        return $pos;
    }
    private static function data(): Fluent
    {
        if (!isset(self::$data)) self::$data = new Fluent();
        return self::$data;
    }

    public static function normalizers(): array
    {
        return [namespace\Normalizers\ArtisanCommandInputNormalizer::class];
    }

    public function isRequired(): bool
    {
        return flags_has($this->mode, $this->type->getRequiredFlag());
    }

    public function isOptional(): bool
    {
        return flags_has($this->mode, $this->type->getOptionalFlag());
    }

    public function isArray(): bool
    {
        return flags_has($this->mode, $this->type->getArrayFlag());
    }

    public function isNone(): bool
    {
        return flags_has($this->mode, $this->type->getNoneFlag());
    }

    public function isNegatable(): bool
    {
        return flags_has($this->mode, $this->type->getNegatableFlag());
    }
}
