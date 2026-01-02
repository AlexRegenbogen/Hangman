<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    /** @var array|string[] */
    protected array $listen = [
    ];

    /** Determine if events and listeners should be automatically discovered. */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
