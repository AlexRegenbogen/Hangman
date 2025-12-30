<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\CharacterUsedException;
use App\Exceptions\GameOverException;
use App\Exceptions\GameWonException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * @property string $word
 */
class Game extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public const string MASK_CHAR = '.';

    public const int MAX_TRIES = 6;

    // Switch between database and API for word generation/selection
    //      1 - Use word-table in database,
    //      0 - will use API on SetGetGo
    public const int USE_DATABASE = 1;

    public const int BUSY = 0;

    public const int FAIL = 1;

    public const int SUCCESS = 2;

    /** @var array|string[] */
    private array $statusInformation = ['busy', 'fail', 'success'];

    public ?string $error = null;

    protected $table = 'game';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'word',
        'tries_left',
        'characters_guessed',
        'status',
    ];

    protected $casts = [
        'characters_guessed' => 'array',
    ];

    /** @return Attribute<string, string> */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): string => $this->statusInformation[$value]
        );
    }

    public static function startNew(string $locale = 'en'): self
    {
        $game = new self();
        $game->fill([
            'word' => self::getRandomWord($locale),
            'tries_left' => self::MAX_TRIES,
            'characters_guessed' => [],
            'status' => self::BUSY,
        ]);
        $game->save();

        return $game;
    }

    /**
     * @throws GameWonException
     * @throws CharacterUsedException
     * @throws GameOverException
     */
    public static function guess(string $id, string $char): self
    {
        $game = self::where('status', self::BUSY)->findOrFail($id);
        $chars = $game->characters_guessed;

        if (\in_array(strtolower($char), $chars)) {
            $game->error = \sprintf('Character %s already used!', $char);

            return $game;
        }

        if (!str_contains((string) $game->word, $char)) {
            --$game->tries_left;
        }

        $chars[] = $char;
        $game->characters_guessed = $chars;

        if ($game->won()) {
            $game->status = self::SUCCESS;
            $game->save();
            $game->error = \sprintf('Congratulations! %s is the correct word.', $game->word);

            return $game;
        }

        if (0 === $game->tries_left) {
            $game->status = self::FAIL;
            $game->save();
            $game->error = \sprintf('You lost. The word was: %s', $game->word);

            return $game;
        }

        $game->save();

        return $game;
    }

    private static function getRandomWord(string $locale = 'en'): string
    {
        if (self::USE_DATABASE === 0) {
            $response = Http::get('https://random-word-api.herokuapp.com/word?lang='.$locale);

            return strtolower((string) json_decode((string) $response->getBody()[0]));
        }

        return Word::whereLocale($locale)->inRandomOrder()->first()->word;
    }

    public function getWord(): string
    {
        $chars = $this->characters_guessed;
        $invalidChars = array_diff(range('a', 'z'), $chars);

        return str_replace($invalidChars, self::MASK_CHAR, $this->word);
    }

    public function won(): bool
    {
        return !str_contains($this->getWord(), self::MASK_CHAR);
    }
}
