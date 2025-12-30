<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ExampleTest extends TestCase
{
    /** A basic test example. */
    public function testTheApplicationReturnsASuccessfulResponse(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        /** @var string $content */
        $content = $response->getContent();
        self::assertStringContainsString('', $content);
    }
}
