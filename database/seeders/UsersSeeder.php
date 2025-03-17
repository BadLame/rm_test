<?php

namespace Database\Seeders;

use App\Models\User;
use App\ValueObjects\User\SitePartitionAccessVO;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Создание админа
        User::query()->where('is_admin', true)->firstOr(
            fn () => User::factory()->create([
                'is_admin' => true,
                'login' => 'admin',
                'password' => Hash::make('admin'),
                'name' => 'Величайший',
                'surname' => 'Админ',
                'blocked_at' => null,
                'access' => new SitePartitionAccessVO(forAdmin: true),
            ])
        );

        // Добавить пользователей, если ещё не были добавлены
        if (User::query()->count() <= 1) {
            User::factory(50)->create();
        }
    }
}
