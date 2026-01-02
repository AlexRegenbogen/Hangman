<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameApiResource;
use App\Models\Game;
use App\Support\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GameController extends Controller
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    public function startNewGame(Request $request): GameApiResource
    {
        $locale = $request->string('locale', 'en');

        return $this->gameService->startNew($locale->toString());
    }

    public function guess(string $gameId, Request $request): JsonResponse|GameApiResource
    {
        $game = Game::find($gameId);

        if (null === $game) {
            return response()->json([
                'status' => 'error',
                'message' => 'Game not found!',
                'code' => 'GAME_NOT_FOUND',
            ], 404);
        }

        $char = $request->string('character');

        return $this->gameService->getGuessResponse($gameId, $char->toString());
    }
}
