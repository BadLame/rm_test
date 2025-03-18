<?php

namespace App\Models;

use App\Casts\User\SitePartitionAccessCast;
use App\Models\Queries\UserQuery;
use App\ValueObjects\User\SitePartitionAccessVO;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;

/**
 * @property int $id
 * @property string $login
 * @property string $name
 * @property string $surname
 * @property string $password
 * @property Carbon|null $blocked_at
 * @property bool $is_admin
 * @property SitePartitionAccessVO $access Хранит разрешения на доступ к разделам сайта // fixme remove
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static UserQuery|User query()
 *
 * @mixin UserQuery
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $attributes = [
        'is_admin' => false,
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'blocked_at' => 'datetime',
        'access' => SitePartitionAccessCast::class,
    ];

    protected $fillable = [
        'login',
        'password',
        'name',
        'surname',
        'is_admin',
        'blocked_at',
        'access',
    ];

    protected $hidden = [
        'password',
        'is_admin',
    ];

    // Misc

    function newEloquentBuilder($query): UserQuery
    {
        return new UserQuery($query);
    }
}
