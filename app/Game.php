<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Requests;
use App\Exceptions\CharacterUsedException;
use App\Exceptions\GameOverException;
use App\Exceptions\GameWonException;

class Game extends Model
{
    use HasUuid;

    public const MASK_CHAR = '.';

    public const MAX_TRIES = 6;

    // Switch between database and API for word generation/selection
    //      1 - Use word-table in database,
    //      0 - will use API on SetGetGo
    public const USE_DATABASE = 1;

    public const BUSY                 = 0;
    public const FAIL                 = 1;
    public const SUCCESS              = 2;
    private array $statusInformation = ["busy", "fail", "success"];

    protected $table = 'game';

    public $incrementing = false;
    public $timestamps   = false;

    protected $fillable = [
        'word'
    ];

    protected $casts = [
        'characters_guessed' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        $attributes['word'] = $this->getRandomWord();

        parent::__construct($attributes);
    }

    public function getStatusAttribute($value)
    {
        return $this->statusInformation[$value];
    }

    public static function startNew()
    {
        $game = new self();
        $game->setAttribute('tries_left', self::MAX_TRIES);
        $game->setAttribute('word', $game->getRandomWord());
        $game->setAttribute('status', self::BUSY);
        $game->setAttribute('characters_guessed', []);
        $game->save();
        return $game;
    }

    public static function guess($id, $char)
    {
        $game  = Game::where('status', self::BUSY)->findOrFail($id);
        $chars = $game->characters_guessed;

        if (in_array(strtolower($char), $chars)) {
            throw (new CharacterUsedException($char . ' has already been used'))->setGame($game);
        }

        if (strpos($game->word, $char) === false) {
            $game->tries_left--;
        }

        $chars[]                  = $char;
        $game->characters_guessed = $chars;

        if ($game->won()) {
            $game->status = self::SUCCESS;
            $game->save();
            throw (new GameWonException('Congratulations! ' . $game->word . ' is the correct word.'))->setGame($game);
        }

        if ($game->tries_left <= 0) {
            $game->status = self::FAIL;
            $game->save();
            throw (new GameOverException('You lost. The word was: ' . $game->word))->setGame($game);
        }

        $game->save();

        return $game;
    }

    private function getRandomWord()
    {
        if (self::USE_DATABASE === 0) {
            $request = Requests::get('https://random-word-api.herokuapp.com/word');
            return strtolower(json_decode($request->body)[0]);
        }

        return \App\Word::orderBy(\DB::raw('RAND()'))->first()->word;
    }

    public function getWord()
    {
        $chars        = $this->characters_guessed;
        $invalidChars = array_diff(range('a', 'z'), $chars);
        $word         = str_replace($invalidChars, self::MASK_CHAR, $this->word);
        return $word;
    }

    public function won()
    {
        return strpos($this->getWord(), self::MASK_CHAR) === false;
    }
}
