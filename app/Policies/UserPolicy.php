<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        return $user->id === 1 ? : null;
    }

    public function changeEmail(User $user, User $model)
    {
        return $user->id === $model->id;
    }
}
