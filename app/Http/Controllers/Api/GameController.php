<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Game;
use App\GameApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\GameApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function startNewGame(Request $request): GameApiResource
    {
        $locale = $request->string('locale', 'en');

        return GameApi::startNew($locale->toString());
    }

    /** @throws \JsonException */
    public function guess(Game $game, Request $request): JsonResponse
    {
        if ($game->getKey() === null) {
            abort(404);
        }
        $char = $request->string('character');

        return GameApi::guess((string) $game->getKey(), $char->toString());
    }
}
