<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HomePageTest extends TestCase
{
    #[Test]
    public function theApplicationReturnsASuccessfulResponse(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        /** @var string $content */
        $content = $response->getContent();
        self::assertStringContainsString('', $content);
    }
}
