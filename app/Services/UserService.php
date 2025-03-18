<?php

namespace App\Services;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Dto\UserDto;
use App\Models\User;

class UserService
{
    function create(UserDto $dto): User
    {
        return app(CreateUserAction::class)->exec($dto);
    }

    function update(User $user, UserDto $dto): User
    {
        return app(UpdateUserAction::class)->exec($user, $dto);
    }
}
