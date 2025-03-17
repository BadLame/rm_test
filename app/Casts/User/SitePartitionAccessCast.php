<?php

namespace App\Casts\User;

use App\ValueObjects\User\SitePartitionAccessVO;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SitePartitionAccessCast implements CastsAttributes
{
    function get(Model $model, string $key, mixed $value, array $attributes): SitePartitionAccessVO
    {
        return new SitePartitionAccessVO($attributes['access'], $attributes['is_admin'] ?? false);
    }

    /**
     * @param Model $model
     * @param string $key
     * @param SitePartitionAccessVO $value
     * @param array $attributes
     * @return string
     */
    function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return $value->jsonSerialize();
    }
}
