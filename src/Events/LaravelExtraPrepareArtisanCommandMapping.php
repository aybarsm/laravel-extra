<?php

declare(strict_types=1);

namespace Aybarsm\Laravel\Extra\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LaravelExtraPrepareArtisanCommandMapping
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
