<?php

namespace App\Models\Traits;

use App\Models\Scopes\UserScope;
use Illuminate\Support\Facades\Schema;

trait BelongToUser
{
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);

        static::creating(function ($model) {
            if (auth()->check() && empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }
}
