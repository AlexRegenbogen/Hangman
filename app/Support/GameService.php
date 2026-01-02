<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\StatusInformation;
use App\Http\Resources\GameApiResource;
use App\Models\Game;
use App\Models\Word;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GameService
{
    public function __construct(
        public bool $useDatabase,
        public string $maskChar,
        public int $maxTries
    ) {
    }

    public function startNew(string $locale = 'en'): GameApiResource
    {
        $word = $this->getRandomWord($locale);
        $game = new Game();
        $game->fill([
            'word' => $word,
            'tries_left' => $this->maxTries,
            'characters_guessed' => [],
            'status' => StatusInformation::BUSY,
        ]);
        $game->save();

        return GameApiResource::make($game);
    }

    public static function resumeRunning(Game $game): GameApiResource
    {
        return GameApiResource::make($game);
    }

    public function getGuessResponse(string $id, string $char): GameApiResource
    {
        $game = Game::where('status', StatusInformation::BUSY)->findOrFail($id);

        return GameApiResource::make(self::processGuessedCharacter($game, $char));
    }

    public static function processGuessedCharacter(Game $game, string $char): Game
    {
        $chars = $game->characters_guessed;

        if (\in_array(strtolower($char), $chars, true)) {
            $game->error = \sprintf('Character %s already used!', $char);

            return $game;
        }

        if (!str_contains((string) $game->word, $char)) {
            --$game->tries_left;
        }

        $chars[] = $char;
        $game->characters_guessed = $chars;

        if ($game->won()) {
            $game->status = StatusInformation::SUCCESS;
            $game->save();
            $game->error = \sprintf('Congratulations! %s is the correct word.', $game->word);

            return $game;
        }

        if (0 === $game->tries_left) {
            $game->status = StatusInformation::FAIL;
            $game->save();
            $game->error = \sprintf('You lost. The word was: %s', $game->word);

            return $game;
        }

        $game->save();

        return $game;
    }

    private function getRandomWord(string $locale = 'en'): string
    {
        if (!$this->useDatabase) {
            if (\in_array($locale, ['en', 'es', 'ro', 'it', 'de', 'fr', 'pt-br', 'zh'], true)) {
                try {
                    $response = Http::get('https://random-word-api.herokuapp.com/word?lang='.$locale);

                    /** @var array<int, string> $json */
                    $json = $response->json();
                } catch (ConnectionException) {

                }
            } else {
                throw new \RuntimeException('Translations in this language not available!');
            }

            return strtolower($json[0] ?? 'hangman');
        }

        $wordModel = Word::whereLocale($locale)->inRandomOrder()->first();

        return strtolower($wordModel instanceof Word ? $wordModel->word : 'hangman');
    }
}
