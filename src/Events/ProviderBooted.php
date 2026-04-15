<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\ServiceProvider;
final class ProviderBooted
{
    use Dispatchable;
    public function __construct(
        public ServiceProvider $provider,
    )
    {}
}
