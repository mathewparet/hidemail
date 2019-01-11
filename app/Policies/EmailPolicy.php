<?php

namespace App\Policies;

use App\User;
use App\Email;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        return $user->id === 1 ? true : null;
    }

    public function show(User $user, Email $email)
    {
        return $user->owns($email);
    }

    public function destroy(User $user, Email $email)
    {
        return $user->owns($email);
    }

}
