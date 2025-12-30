<?php

declare(strict_types=1);

use App\Http\Controllers\Api\GameController;
use Illuminate\Support\Facades\Route;

// games
Route::post('games', [GameController::class, 'startNewGame']);


// games/[:id]
Route::put('games/{game}', [GameController::class, 'guess']);
