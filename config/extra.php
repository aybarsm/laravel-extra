<?php

declare(strict_types=1);

return [
    'cache' => [
        'enabled' => app()->isProduction(),
        'store' => env('CACHE_STORE', 'database'),
        'key' => 'laravel-extra',
    ],
    'mixins' => [
        \Aybarsm\Laravel\Extra\Mixins\ApplicationMixin::class,
    ],
];
