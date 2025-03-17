<?php

namespace Database\Factories;

use App\Models\Enums\User\SitePartitionAccessEnum;
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

    private function getSitePartitionsAccess(bool $isAdmin): SitePartitionAccessVO
    {
        $partitionsAccess = collect(array_column(SitePartitionAccessEnum::cases(), 'value'))
            ->when(
                $isAdmin,
                fn (Collection $partitions) => $partitions->mapWithKeys(fn (string $partition) => [$partition => true]),
                fn (Collection $partitions) => $partitions->mapWithKeys(fn (string $partition) => [
                    $partition => ($partition === SitePartitionAccessEnum::ADMINISTRATIVE->value) ? false : !rand(0, 1),
                ])
            );

        return new SitePartitionAccessVO($partitionsAccess->toJson());
    }
}
