<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Game;
use App\GameApi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $request->get('locale', 'en');

        return view('game');
    }

    public function continue(Game $game): View
    {
        return view('game', ['game' => GameApi::resumeRunning($game)]);
    }
}
