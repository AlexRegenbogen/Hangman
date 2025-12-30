<?php

declare(strict_types=1);

namespace App;

class GameApi
{
    private array $chars = [];

    public static function startNew(): array
    {
        return self::getStatus(Game::startNew());
    }

    public static function guess($id, $char): false|array|string
    {
        try {
            $response = self::getStatus(Game::guess($id, $char));
        } catch (Exceptions\BaseGameException $e) {
            $response = self::getStatus($e->getGame(), $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response = json_encode(['error' => 'Game not found!'], \JSON_THROW_ON_ERROR);
        }

        return $response;
    }

    private static function getStatus($game, $error = null): array
    {
        return (new ApiResponse($game, $error))->render();
    }
}
