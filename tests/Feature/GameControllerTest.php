<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusInformation;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itCanStartANewGame(): void
    {
        // Mock the external API call triggered by GameService
        Http::fake([
            'random-word-api.herokuapp.com/*' => Http::response(['hangman']),
        ]);

        $response = $this->postJson('/api/games', [
            'locale' => 'en',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'word',
                    'tries_left',
                    'status',
                ],
            ])
            ->assertJsonPath('data.word', '.......') // "hangman" masked
            ->assertJsonPath('data.tries_left', config('hangman.max_tries'));
    }

    #[Test]
    public function itReturnsCustomErrorWhenGameNotFound(): void
    {
        $response = $this->putJson('/api/games/invalid-uuid', [
            'character' => 'a',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Game not found!',
                'code' => 'GAME_NOT_FOUND',
            ]);
    }

    #[Test]
    public function itCanSubmitAGuess(): void
    {
        // 1. Create a game directly in the DB
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 11,
            'characters_guessed' => [],
            'status' => StatusInformation::BUSY,
        ]);

        // 2. Submit a guess via the API
        $response = $this->putJson('/api/games/'.$game->id, [
            'character' => 'a',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.word', 'a....') // "apple" with 'a' revealed
            ->assertJsonPath('data.tries_left', 11);
    }

    #[Test]
    public function itHandlesIncorrectGuessViaController(): void
    {
        $game = Game::create([
            'word' => 'apple',
            'tries_left' => 11,
            'characters_guessed' => [],
            'status' => StatusInformation::BUSY,
        ]);

        $response = $this->putJson('/api/games/'.$game->id, [
            'character' => 'z',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.tries_left', 10)
            ->assertJsonPath('data.word', '.....');
    }

    #[Test]
    public function itRendersTheHangmanComponentWhenContinuingAGame(): void
    {
        $game = Game::create([
            'word' => 'laravel',
            'tries_left' => 5,
            'characters_guessed' => ['a', 'e'],
            'status' => StatusInformation::BUSY,
        ]);

        $response = $this->get('/'.$game->id);

        $response->assertStatus(200);

        $response->assertInertia(
            static fn (Assert $page): Assert => $page
            ->component('Game')
            ->has(
                'initialGame',
                static fn (Assert $prop): Assert => $prop
                ->where('id', $game->id)
                ->where('word', '.a.a.e.')
                ->where('tries_left', 5)
                ->etc()
            )
        );
    }
}
