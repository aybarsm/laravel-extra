<?php

declare(strict_types=1);

if (!function_exists('vendor_path')) {
    function vendor_path(string|\Stringable... $paths): string {
        $basePath = Illuminate\Support\Env::get('COMPOSER_VENDOR_DIR', base_path('vendor'));
        return fs_path($basePath, ...$paths);
    }
}

if (!function_exists('debug_context')) {
    function debug_context(
        array $context,
        int $usleep = 0,
        string $key = 'debug_context',
        bool $reset = false,
    ): void {
        if ($usleep > 0) usleep($usleep);

        $context['ts'] = now('UTC')->toIso8601ZuluString('microsecond');
        if ($reset) {
            \Illuminate\Support\Facades\Context::add($key, [$context]);
        }else {
            \Illuminate\Support\Facades\Context::push($key, $context);
        }
    }
}
