<?php

namespace App\Http\Controllers;

use App\Dto\UserUpdateDto;
use App\Http\Requests\BlockRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\UserBaseResource;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsersController extends Controller
{
    const int PER_PAGE = 10;

    /**
     * @lrd:start
     * Просмотр списка пользователей с пагинацией и возможностью поиска по имени/фамилии
     * @lrd:end
     */
    function index(SearchRequest $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->search($request->search)
            ->paginate(self::PER_PAGE);

        return UserBaseResource::collection($users);
    }

    /**
     * @lrd:start
     * Просмотр полной информации по пользователю
     * @lrd:end
     */
    function show(User $user): UserDetailResource
    {
        return new UserDetailResource($user);
    }

    /**
     * @lrd:start
     * Редактирование имени/фамилии пользователя
     * @lrd:end
     */
    function update(UpdateRequest $request, User $user, UserService $userService): UserDetailResource
    {
        $user = $userService->update($user, UserUpdateDto::fromArray($request->validated()));

        return new UserDetailResource($user);
    }

    function block(BlockRequest $request, User $user, UserService $userService): UserDetailResource
    {
        $user = $userService->update($user, UserUpdateDto::fromArray($request->validated()));

        return new UserDetailResource($user);
    }

    function changePassword(ChangePasswordRequest $request, User $user, UserService $userService): UserDetailResource
    {
        $user = $userService->update($user, UserUpdateDto::fromArray($request->validated()));

        return new UserDetailResource($user);
    }
}
