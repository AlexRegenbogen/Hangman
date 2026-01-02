<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Game;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    #[Test]
    public function itCorrectlyMasksTheWord(): void
    {
        $game = new Game();
        $game->word = 'hangman';
        $game->characters_guessed = ['a', 'n'];

        // Expected: .a.n.an
        self::assertEquals('.an..an', $game->getWord('.'));
    }

    #[Test]
    public function itHandlesEmptyGuessesGracefully(): void
    {
        $game = new Game();
        $game->word = 'php';
        $game->characters_guessed = [];

        self::assertEquals('...', $game->getWord('.'));
    }

    #[Test]
    public function itDetectsWinCondition(): void
    {
        $game = new Game();
        $game->word = 'cat';

        // Scenario 1: Not won
        $game->characters_guessed = ['c', 'a'];
        self::assertFalse($game->won('.'));

        // Scenario 2: Won
        $game->characters_guessed = ['c', 'a', 't'];
        self::assertTrue($game->won('.'));
    }
}
