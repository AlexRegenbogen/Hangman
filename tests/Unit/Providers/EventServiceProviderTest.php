<?php

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

        $this->assertInstanceOf(EventServiceProvider::class, $provider);
        $this->assertFalse($provider->shouldDiscoverEvents());
    }
}
