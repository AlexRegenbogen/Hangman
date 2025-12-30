<?php

declare(strict_types=1);

namespace App;

use Ramsey\Uuid\Uuid;

trait HasUuid
{
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            $model->incrementing = false;
            $model->{$model->getKeyName()} = (string) Uuid::uuid4();
        });
    }
}
