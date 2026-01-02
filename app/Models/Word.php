<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Word extends Model
{
    protected $table = 'word';

    public $timestamps = false;
}
