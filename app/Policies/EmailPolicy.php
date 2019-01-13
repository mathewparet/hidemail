<?php

namespace App\Policies;

use App\User;
use App\Email;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailPolicy
{
    use HandlesAuthorization;

    // public function before(User $user, $ability)
    // {
    //     return $user->id === 1 ? true : null;
    // }

    public function show(User $user, Email $email)
    {
        return $user->owns($email) && $user->tokenCan('view-hidden-email-ids');
    }

    public function destroy(User $user, Email $email)
    {
        return $user->owns($email) && $user->tokenCan('delete-hidden-email-ids');
    }

    public function store(User $user)
    {
        return $user->tokenCan('create-hidden-email-ids');
    }

}
