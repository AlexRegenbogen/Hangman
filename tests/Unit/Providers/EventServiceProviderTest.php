<?php

declare(strict_types=1);

namespace Unit\Providers;

use App\Providers\EventServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EventServiceProviderTest extends TestCase
{
    #[Test]
    public function baseEventServiceProviderCovered(): void
    {
        $provider = new EventServiceProvider($this->app);

        self::assertInstanceOf(EventServiceProvider::class, $provider);
        self::assertFalse($provider->shouldDiscoverEvents());
    }
}
