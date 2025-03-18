<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\UserBaseResource;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UsersController extends Controller
{
    const int PER_PAGE = 10;

    /**
     * @lrd:start
     * Просмотр списка пользователей с пагинацией и возможностью поиска по имени/фамилии
     * @lrd:end
     *
     * @return AnonymousResourceCollection
     */
    function index(SearchRequest $request)
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
     * @param User $user
     * @return UserDetailResource
     */
    function show(User $user)
    {
        return new UserDetailResource($user);
    }
}
