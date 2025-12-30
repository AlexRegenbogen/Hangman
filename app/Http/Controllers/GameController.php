<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\GameApi;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function startNewGame(): array
    {
        return GameApi::startNew();
    }

    /** @throws \JsonException */
    public function guess($id, Request $request): false|array|string
    {
        $char = $request->input('character');

        return GameApi::guess($id, $char);
    }
}
