<?php

namespace App\Exceptions;

use App\Game;

class BaseGameException extends \Exception
{
    private $game;

    public function setGame(Game $game)
    {
        $this->game = $game;
        return $this;
    }

    public function getGame()
    {
        return $this->game;
    }
}
