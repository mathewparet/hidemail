<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if(in_array($ability, [
            'index',
            'toggleSuspension'
        ]))
            return null;

        return $user->id === 1 ? : null;
    }

    public function changeEmail(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    public function update(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    public function show(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    public function toggleSuspension(User $user, User $model)
    {
        return $user->id === 1
            && $model->id != 1;
    }

    public function index(User $user)
    {
        return $user->id === 1;
    }

    public function link(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    public function deLink(User $user, User $model)
    {
        return $user->id === $model->id;
    }
}
