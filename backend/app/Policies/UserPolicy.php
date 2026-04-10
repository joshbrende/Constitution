<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function update(User $user, User $model): bool
    {
        return (int) $user->id === (int) $model->id;
    }

    /** Revoke own API session (logout). */
    public function logoutApi(User $user, User $target): bool
    {
        return (int) $user->id === (int) $target->id;
    }
}
