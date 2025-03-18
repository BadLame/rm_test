<?php

namespace App\Services;

use App\Actions\UpdateUserAction;
use App\Dto\UserUpdateDto;
use App\Models\User;

class UserService
{
    function update(User $user, UserUpdateDto $dto): User
    {
        return app(UpdateUserAction::class)->exec($user, $dto);
    }
}
