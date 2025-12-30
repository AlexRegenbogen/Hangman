<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\BaseGameException;
use App\Http\Resources\GameApiResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class GameApi
{
    public static function startNew(string $locale = 'en'): GameApiResource
    {
        return self::getStatus(Game::startNew($locale));
    }

    public static function resumeRunning(Game $game): GameApiResource
    {
        return self::getStatus($game);
    }

    public static function guess(string $id, string $char): JsonResponse
    {
        try {
            return response()->json(self::getStatus(Game::guess($id, $char)));
        } catch (BaseGameException $e) {
            /** @var Game $game */
            $game = $e->getGame();
            return response()->json(self::getStatus($game, $e->getMessage()));
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Game not found!'], \JSON_THROW_ON_ERROR);
        }
    }

    private static function getStatus(Game $game, string $error = null): GameApiResource
    {
        if (null !== $error) {
            $game->error = $error;
        }

        return new GameApiResource($game);
    }
}
