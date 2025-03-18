<?php

namespace App\Models\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin User
 */
class UserQuery extends Builder
{
    function search(?string $search): self
    {
        if (!$search) return $this;

        return $this->where(
            fn (self $q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
        );
    }
}
