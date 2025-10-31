<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    /**
     * Generate UUID key for the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
