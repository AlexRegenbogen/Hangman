<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Game;
use App\Support\GameService;
use Inertia\Inertia;
use Inertia\Response;

final class GameController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Game', []);
    }

    public function continue(Game $game): Response
    {
        return Inertia::render('Game', [
            'initialGame' => GameService::resumeRunning($game)->resolve(),
        ]);
    }
}
