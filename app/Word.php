<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'word';

    public $timestamps = false;
}
