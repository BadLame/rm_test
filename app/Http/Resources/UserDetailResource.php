<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'login' => $user->login,
            'is_admin' => (bool)$user->is_admin,
            'blocked_at' => $user->blocked_at?->timestamp,
            'created_at' => $user->created_at->timestamp,
            'updated_at' => $user->updated_at->timestamp,
        ];
    }
}
