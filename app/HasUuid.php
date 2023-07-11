<?php

namespace App;

use Ramsey\Uuid\Uuid;

trait HasUuid
{
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->incrementing = false;
            $model->{$model->getKeyName()} = (string) Uuid::uuid4();
        });
    }
}
