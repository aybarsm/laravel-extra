<?php

declare(strict_types=1);

return [
    'cache' => [
        'enabled' => app()->isProduction(),
        'store' => env('CACHE_STORE', 'database'),
        'key' => 'laravel-extra',
    ],
    /**
     * Mixin classes can have `const string BIND` value or can be mapped with assoc binding
     */
    'mixins' => [
        vendor_path('aybarsm', 'laravel-extra', 'mixins'),
//        \Aybarsm\Laravel\Extra\Mixins\ApplicationMixin::class,
//        \Aybarsm\Laravel\Extra\Mixins\StrMixin::class,
//        \Aybarsm\Laravel\Extra\Mixins\StringableMixin::class,
    ],
];
