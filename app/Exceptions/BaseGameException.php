<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Game;

class BaseGameException extends \Exception
{
    private ?Game $game = null;

    public function setGame(Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }
}
