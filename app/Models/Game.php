<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusInformation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string         $word
 * @property array|string[] $characters_guessed
 */
final class Game extends Model
{
    use HasUuids;

    protected $keyType = 'string';

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
        'status' => StatusInformation::class,
    ];

    public function won(string $maskChar = '.'): bool
    {
        return !str_contains($this->getWord(), $maskChar);
    }

    public function getWord(string $maskChar = '.'): string
    {
        $chars = $this->characters_guessed;
        $invalidChars = array_diff(range('a', 'z'), $chars);

        return str_replace($invalidChars, $maskChar, $this->word);
    }
}
