<?php

namespace App;

class ApiResponse
{
    private $game;

    private $error = [];

    public function __construct(Game $game, $error = null)
    {
        $this->game = $game;
        if ($error) {
            $this->error = ['error' => $error];
        }
    }

    public function render()
    {
        return [
            'id'         => $this->game->id,
            'tries_left' => $this->game->tries_left,
            'word'       => $this->game->getWord(),
            'chars'      => $this->game->characters_guessed,
            'status'     => $this->game->status,
        ] + $this->error;
    }
}
