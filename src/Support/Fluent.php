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

    public function get($key, mixed $default = null): mixed
    {
        return parent::get(data_key($key), $default);
    }

    public function set($key, mixed $value): static
    {
        return parent::set(data_key($key), $value);
    }

    public function has($key): bool
    {
        return parent::has($this->multiKey($key));
    }

    public function hasAny($keys): bool
    {
        return parent::hasAny($this->multiKey($keys));
    }

    public function whenHas($key, callable $callback, ?callable $default = null)
    {
        return parent::whenHas(data_key($key), $callback, $default);
    }

    public function value($key, $default = null): mixed
    {
        $key = data_key($key);
        return value($this->has($key) ? $this->get($key) : $default);
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

    public function filled($key): bool
    {
        return ModeMatch::ALL->matchesBy(
            of: array_wrap($key),
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

    public function hasOr(string|int|array $keys, mixed $default): mixed
    {
        foreach($this->multiKey($keys) as $key) {
            if ($this->has($key)) {
                return $this->get($key);
            }
        }

        return value($default);
    }

    public function filledOr(string|int|array $keys, mixed $default): mixed
    {
        foreach($this->multiKey($keys) as $key) {
            $ret = $this->get($key);
            if (filled($ret)) {
                return $ret;
            }
        }

        return value($default);
    }

    public function getOrSet(string|int $key, mixed $default): mixed
    {
        $key = data_key($key);

        if ($this->has($key)) {
            return $this->get($key);
        }

        $default = value($default);
        $this->set($key, $default);
        return $default;
    }

    protected function multiKey($keys): array
    {
        return array_unique(array_map(static fn ($key) => data_key($key), array_wrap($keys)));
    }
}
