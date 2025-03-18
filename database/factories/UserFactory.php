<?php

namespace Database\Factories;

use App\Models\Enums\User\SiteModulesAccessEnum;
use App\Models\User;
use App\ValueObjects\User\SitePartitionAccessVO;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'login' => fake()->unique()->safeEmail(),
            'name' => fake()->unique()->firstName(),
            'surname' => fake()->unique()->lastName(),
            'password' => Hash::make('password'),
            'is_admin' => fake()->boolean(10),
            'blocked_at' => fake()->boolean(20)
                ? now()->subDays(rand(0, 30))
                : null,
            'access' => fn (array $attributes) => $this->getSitePartitionsAccess($attributes['is_admin']),
        ];
    }

    function blocked(bool $isBlocked = true): self
    {
        return $this->state(['blocked_at' => $isBlocked ? now()->subDays(rand(0, 30)) : null]);
    }

    function admin(bool $isAdmin = true): self
    {
        return $this->state(['is_admin' => $isAdmin]);
    }

    private function getSitePartitionsAccess(bool $isAdmin): SitePartitionAccessVO
    {
        $modulesAccess = collect(array_column(SiteModulesAccessEnum::cases(), 'value'))
            ->when(
                $isAdmin,
                fn (Collection $modules) => $modules->mapWithKeys(fn (string $partition) => [$partition => true]),
                fn (Collection $modules) => $modules->mapWithKeys(fn (string $module) => [
                    $module => ($module === SiteModulesAccessEnum::ADMINISTRATIVE->value) ? false : !rand(0, 1),
                ])
            );

        return new SitePartitionAccessVO($modulesAccess->toJson());
    }
}
