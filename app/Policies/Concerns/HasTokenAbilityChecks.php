<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasTokenAbilityChecks
{
    /*
     * Session-authenticated (no token) users are fully trusted.
     * Token-authenticated requests must carry the specific ability.
     */

    protected function hasAbility(User $user, string $ability): bool
    {
        return is_null($user->currentAccessToken()) || $user->tokenCan($ability);
    }

    protected function owns(User $user, Model $model)
    {
        return $user->id === $model->user_id; // i feel this is not good 
    }
}
