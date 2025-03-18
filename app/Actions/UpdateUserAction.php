<?php

namespace App\Actions;

use App\Dto\UserUpdateDto;
use App\Models\User;

class UpdateUserAction
{
    function exec(User $user, UserUpdateDto $dto): User
    {
        $user->forceFill($dto->toAttributesArray());
        $user->save();

        return $user;
    }
}
