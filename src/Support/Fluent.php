<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Support;

use Aybarsm\Extra\Enums\ModeMatch;
use Illuminate\Support\Arr;

final class Fluent extends \Illuminate\Support\Fluent
{
    protected string $lastHash;
    public function __construct(
        $attributes = [],
        public readonly ?\Closure $saveUsing = null,
    )
    {
        parent::__construct($attributes);
        $this->lastHash = $this->getCurrentHash();
        if (is_callable($this->saveUsing)){
            register_shutdown_function(fn () => $this->save());
        }
    }

    public function isBlank(): bool
    {
        return blank($this->attributes);
    }

    public function isFilled(): bool
    {
        return filled($this->attributes);
    }

    public function save(): self
    {
        if (is_callable($this->saveUsing) && $this->getLastHash() !== $this->getCurrentHash()) {
            call_user_func($this->saveUsing, $this);
            $this->lastHash = $this->getCurrentHash();
        }

        return $this;
    }

    public function getLastHash(): string
    {
        return $this->lastHash;
    }

    public function getCurrentHash(): string
    {
        return hash('xxh128', $this->toJson());
    }

    public function hasHashChanged(): bool
    {
        return $this->lastHash !== $this->getCurrentHash();
    }

    public function filled(string|int|array $key, string|ModeMatch $match = ModeMatch::ALL): bool
    {
        $match = ModeMatch::make($match);
        return $match->matchesBy(
            of: $key,
            callback: static fn ($item) => filled($this->get($item))
        );
    }

    public function blank(string|int|array $key, string|ModeMatch $match = ModeMatch::ANY): bool
    {
        $match = ModeMatch::make($match);
        return $match->matchesBy(
            of: $key,
            callback: static fn ($item) => filled($this->get($item))
        );
    }

    public function push(string|int $key, mixed ...$values): void
    {
        if (blank($values)) return;
        $current = $this->get($key, []);
        array_push($current, ...$values);
        $this->set($key, $current);
    }

    public function unshift(string|int $key, mixed ...$values): void
    {
        if (blank($values)) return;
        $current = $this->get($key, []);
        array_unshift($current, ...$values);
        $this->set($key, $current);
    }

    public function where(string|int $key, callable $callback): array
    {
        return Arr::reject($this->get($key, []), $callback);
    }

    public function reject(string|int $key, callable $callback): void
    {
        $this->set($key, Arr::reject($this->get($key, []), $callback));
    }
    public function forget(
        string|int|array $keys,
        string|int $prefix = '',
        string|int $suffix = '',
    ): void
    {
        foreach(array_wrap($keys) as $key) {
            data_forget($this->attributes, data_key($key, $prefix, $suffix));
        }
    }

    public function reset(iterable $attributes): Fluent
    {
        $this->attributes = iterator_to_array($attributes);
        return $this;
    }

    public function increase(string|int $key, float|int $by, float|int $start = -1): float|int
    {
        $key = data_key($key);
        $current = $this->get($key, $start);
        $current += $by;
        $this->set($key, $current);
        return $current;
    }

    public function decrease(string|int $key, float|int $by, float|int $start = 0): float|int
    {
        $key = data_key($key);
        $current = $this->get($key, $start);
        $current -= $by;
        $this->set($key, $current);
        return $current;
    }
}
