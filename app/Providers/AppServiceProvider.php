<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\GameService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    #[\Override]
    public function register(): void
    {
        $this->app
            ->when(GameService::class)
            ->needs('$useDatabase')
            ->giveConfig('hangman.use_database');
        $this->app
            ->when(GameService::class)
            ->needs('$maskChar')
            ->giveConfig('hangman.mask_char');
        $this->app
            ->when(GameService::class)
            ->needs('$maxTries')
            ->giveConfig('hangman.max_tries');
    }
}
