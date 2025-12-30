<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;

final class JobServiceProvider extends ServiceProvider
{
    /** @var array<string, string> */
    protected array $jobBindings = [
        // Job::class => JobHandler::class,
    ];

    public function boot(Dispatcher $dispatcher): void
    {
        $dispatcher->map($this->jobBindings);
    }
}
