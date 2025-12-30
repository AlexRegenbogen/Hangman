<?php

class ExampleTest extends TestCase
{
    public function testBasicExample(): void
    {
        $response = $this->get('/');

        $response->assertResponseOk();
        $response->assertViewHas('title');
    }
}
