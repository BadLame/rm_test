<?php

namespace App\Actions;

use App\Dto\UserDto;
use App\Models\User;

class UpdateUserAction
{
    function exec(User $user, UserDto $dto): User
    {
        $user->forceFill($dto->toAttributesArray());
        $user->save();

        return $user;
    }
}
