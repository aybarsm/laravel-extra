<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Dto;
use Aybarsm\Extra\Enums\ModeType;
use Aybarsm\Laravel\Extra\Contracts\Dto\DiscoveredValueContract;

final class DiscoveredValue implements DiscoveredValueContract
{
    public function __construct(
        public mixed $value,
    ){
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getValueAs(ModeType|string $to): mixed
    {
        return ModeType::convert($this->getValue(), $to);
    }

    public function is(ModeType|string ...$of): bool
    {
        return ModeType::is($this->getValue(), ...$of);
    }
    public function convertable(ModeType|string $to): bool
    {
        return ModeType::convertable($this->getValue(), $to, false);
    }

    public function convert(ModeType|string $to): static
    {
        $this->value = ModeType::convert($this->getValue(), $to);
        return $this;
    }

    public function isA(string $class): bool
    {
        if ($this->is(ModeType::OBJECT)){
            return is_a($this->getValue(), $class);
        }elseif ($this->convertable(ModeType::STRING)){
            $value = $this->getValueAs(ModeType::STRING);
            return ModeType::is($value, ModeType::CLASS_EXISTS) && is_a($value, $class, true);
        }
        return false;
    }

    public function isSubOf(string $class): bool
    {
        if ($this->is(ModeType::OBJECT)){
            return is_subclass_of($this->getValue(), $class, false);
        }elseif ($this->convertable(ModeType::STRING)){
            $value = $this->getValueAs(ModeType::STRING);
            return ModeType::is($value, ModeType::CLASS_EXISTS) && is_subclass_of($value, $class);
        }

        return false;
    }
}
