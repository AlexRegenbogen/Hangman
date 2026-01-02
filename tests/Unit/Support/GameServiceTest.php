<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Enums\StatusInformation;
use App\Models\Game;
use App\Models\Word;
use App\Support\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    private GameService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize service with test defaults
        $this->service = new GameService(
            useDatabase: false, // Mocking API in this mode
            maskChar: '.',
            maxTries: 6
        );
    }

    #[Test]
    public function itStartsANewGameFetchingWordFromApi(): void
    {
        Http::fake([
            'random-word-api.herokuapp.com/*' => Http::response(['elephant']),
        ]);

        $resource = $this->service->startNew('en');
        $game = $resource->resource;

        /** @var Game $game */
        self::assertInstanceOf(Game::class, $game);
        self::assertEquals('elephant', $game->word);
        self::assertEquals(6, $game->tries_left);
        self::assertEquals(StatusInformation::BUSY, $game->status);
        self::assertEmpty($game->characters_guessed);
    }

    #[Test]
    public function itHandlesTheExceptionWhenStartingANewGameFetchingWordFromApi(): void
    {
        Http::fake([
            'random-word-api.herokuapp.com/*' => static function (): never {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error establishing connection to random-word-api.herokuapp.com!');

        $this->service->startNew('en');
    }

    #[Test]
    public function itHandlesTheExceptionWhenStartingANewGameFetchingWordFromApiWithNonSupportedLanguage(): void
    {
        Http::fake([
            'random-word-api.herokuapp.com/*' => Http::response(['Error' => 'No translation for this language']),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported locale: xx. Supported locales: en, es, ro, it, de, fr, pt-br, zh');

        $game = $this->service->startNew('xx');

    }

    #[Test]
    public function processingCorrectGuess(): void
    {
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 6,
            'characters_guessed' => [],
            'status' => StatusInformation::BUSY,
        ]);

        $updatedGame = GameService::processGuessedCharacter($game, 'a');

        self::assertContains('a', $updatedGame->characters_guessed);
        self::assertEquals(6, $updatedGame->tries_left);
        self::assertNull($updatedGame->error);
    }

    #[Test]
    public function processingIncorrectGuessReducesTries(): void
    {
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 6,
            'characters_guessed' => [],
            'status' => StatusInformation::BUSY,
        ]);

        $updatedGame = GameService::processGuessedCharacter($game, 'z');

        self::assertEquals(5, $updatedGame->tries_left);
        self::assertContains('z', $updatedGame->characters_guessed);
    }

    #[Test]
    public function gameWonOnLastCharacter(): void
    {
        $game = Game::create([
            'word' => 'ox',
            'tries_left' => 5,
            'characters_guessed' => ['o'],
            'status' => StatusInformation::BUSY,
        ]);

        $updatedGame = GameService::processGuessedCharacter($game, 'x');

        self::assertEquals(StatusInformation::SUCCESS, $updatedGame->status);
        self::assertStringContainsString('Congratulations', (string) $updatedGame->error);
    }

    #[Test]
    public function gameLostOnZeroTries(): void
    {
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 1,
            'characters_guessed' => ['b'],
            'status' => StatusInformation::BUSY,
        ]);

        $updatedGame = GameService::processGuessedCharacter($game, 'z');

        self::assertEquals(StatusInformation::FAIL, $updatedGame->status);
        self::assertEquals(0, $updatedGame->tries_left);
        self::assertStringContainsString('You lost', (string) $updatedGame->error);
    }

    #[Test]
    public function duplicateGuessReturnsErrorWithoutReducingTries(): void
    {
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 6,
            'characters_guessed' => ['a'],
            'status' => StatusInformation::BUSY,
        ]);

        $updatedGame = GameService::processGuessedCharacter($game, 'a');

        self::assertEquals(6, $updatedGame->tries_left);
        self::assertStringContainsString('already used', (string) $updatedGame->error);
    }
}
