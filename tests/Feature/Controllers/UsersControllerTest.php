<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    function testIndexDontReturnsListForBlockedUser()
    {
        $user = User::factory()->blocked()->create();

        $this->actingAs($user, 'api')
            ->getJson(route('api.users.index'))
            ->assertStatus(401);
    }

    function testIndexReturnsInfoAboutUsers(): void
    {
        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.index'))
            ->assertSuccessful()
            ->assertExactJsonStructure([
                'data' => ['*' => ['id', 'name', 'surname']],
                // проверить, что пришла информация по пагинации
                'links',
                'meta',
            ])
            // Проверить, что не приходит дополнительной информации
            ->assertJsonMissingPath('data.0.is_admin');
    }

    function testIndexSearchIsWorking(): void
    {
        $nameSearchStr = 'xxx';
        $surnameSearchStr = 'yyy';

        $userByName = User::factory()->create(['name' => "Q_{$nameSearchStr}_Q"]);
        $userBySurname = User::factory()->create(['surname' => "Q_{$surnameSearchStr}_Q"]);

        $userBaseInfoFn = fn (User $u) => ['id' => $u->id, 'name' => $u->name, 'surname' => $u->surname];

        // Проверить, что пользователь будет найден по имени
        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.index', ['search' => $nameSearchStr]))
            ->assertJsonFragment($userBaseInfoFn($userByName))
            ->assertJsonMissing($userBaseInfoFn($userBySurname));

        // Проверить, что пользователь будет найден по фамилии
        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.index', ['search' => $surnameSearchStr]))
            ->assertJsonFragment($userBaseInfoFn($userBySurname))
            ->assertJsonMissing($userBaseInfoFn($userByName));
    }

    function testShowFailsForBlockedUser()
    {
        $user = User::factory()->blocked()->create();

        $this->actingAs($user, 'api')
            ->getJson(route('api.users.show', ['user' => $user->id]))
            ->assertStatus(401);
    }

    // todo Проверить, когда будут настроены права доступа
    function testShowAnotherUserFailsForOrdinaryUser()
    {
        $anotherUser = User::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.show', ['user' => $anotherUser->id]))
            ->assertStatus(401);
    }

    function testShowAnotherUserGivesInfoToAdmin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $anotherUser = User::factory()->create();

        $this->actingAs($admin, 'api')
            ->getJson(route('api.users.show', ['user' => $anotherUser->id]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'surname',
                    'is_admin',
                    'blocked_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    function testShowReturnsFullInfoAboutCurrentUser()
    {
        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.show', ['user' => $this->user->id]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'surname',
                    'is_admin',
                    'blocked_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->blocked(false)->create();
    }
}
