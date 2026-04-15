<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Support;

use Aybarsm\Extra\Support\Validate as AybarsmValidate;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

final class Discovery extends Collection
{
    private ?Finder $useFinder = null;
    private ?\Closure $extractPathsUsing = null;

    public function useFinder(Finder $finder): self
    {
        $this->useFinder = $finder;
        return $this;
    }

    public function extractPathsUsing(\Closure $callable): self
    {
        $this->extractPathsUsing = $callable;
        return $this;
    }

    public function resolve(): Collection
    {
        $ret = collect();
        $finder = $this->useFinder ?? Finder::create();

        foreach ($this->items as $key => $item) {
            $isObject = is_object($item);
            if ($isObject && is_subclass_of($item, \Stringable::class)) {
                $item = (string) $item;
                $isObject = false;
            }
            $isString = is_string($item);
            $classExists = $isString && class_exists($item);
            $isPath = $isString && AybarsmValidate::path($item);
            $pathExists = $isPath && file_exists($item);
            $isDir = $pathExists && is_dir($item);

        }
    }
}
