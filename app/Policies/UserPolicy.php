<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    function view(User $user, User $model): Response
    {
        return $user->is_admin || ($user->id == $model->id)
            ? Response::allow()
            : Response::denyWithStatus(401);
    }

    function create(User $user): Response
    {
        return $user->is_admin ? Response::allow() : Response::denyWithStatus(401);
    }

    function block(User $user, User $model): Response
    {
        return $user->is_admin ? Response::allow() : Response::denyWithStatus(401);
    }

    function update(User $user, User $model): Response
    {
        return $user->is_admin || ($user->id == $model->id && !$user->blocked_at)
            ? Response::allow()
            : Response::denyWithStatus(401);
    }
}
