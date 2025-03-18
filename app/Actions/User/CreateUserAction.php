<?php

namespace App\Actions\User;

use App\Dto\UserDto;
use App\Models\User;

class CreateUserAction
{
    function exec(UserDto $dto): User
    {
        return User::query()->forceCreate($dto->toAttributesArray());
    }
}
