<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $user;

    // index

    function testIndexDontReturnsListForBlockedUser()
    {
        $user = User::factory()->admin(false)->blocked()->create();

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
                'data' => ['*' => ['id', 'name', 'surname', 'login']],
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

    // show

    function testShowFailsForBlockedUser()
    {
        $user = User::factory()->blocked()->create();

        $this->actingAs($user, 'api')
            ->getJson(route('api.users.show', ['user' => $user->id]))
            ->assertStatus(401);
    }

    function testShowAnotherUserFailsForOrdinaryUser()
    {
        $anotherUser = User::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson(route('api.users.show', ['user' => $anotherUser->id]))
            ->assertStatus(401);
    }

    function testShowGivesUserInfoToAdmin()
    {
        $anotherUser = User::factory()->create();

        $this->actingAs($this->admin, 'api')
            ->getJson(route('api.users.show', ['user' => $anotherUser->id]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'surname',
                    'login',
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
                    'login',
                    'is_admin',
                    'blocked_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    // update

    function testUpdateByAdminCanChangeAnotherUserCredentials()
    {
        $newUserData = [
            'login' => Str::random(),
            'name' => Str::random(),
            'surname' => Str::random(),
            'is_admin' => 1,
        ];

        $this->actingAs($this->admin, 'api')
            ->postJson(route('api.users.update', ['user' => $this->user->id]), $newUserData)
            ->assertSuccessful();

        $this->assertDatabaseHas('users', array_merge($newUserData, ['id' => $this->user->id]));
    }

    function testUpdateCredentialsByTheOwnerShouldSucceed()
    {
        $newUserData = [
            'login' => Str::random(),
            'name' => Str::random(),
            'surname' => Str::random(),
            'is_admin' => 1, // Для доп проверки, что пользователь не сможет сделать себя админом
        ];

        $this->actingAs($this->user, 'api')
            ->postJson(route('api.users.update', ['user' => $this->user->id]), $newUserData)
            ->assertSuccessful();

        $this->assertDatabaseHas('users', array_merge($newUserData, [
            'id' => $this->user->id,
            'is_admin' => 0, // несмотря на посланный `is_admin` пользователь не повысил привилегии
        ]));
    }

    function testUpdateCredentialsOfAnotherUserShouldFail()
    {
        $anotherUser = User::factory()->create();
        $newUserData = [
            'login' => Str::random(),
            'name' => Str::random(),
            'surname' => Str::random(),
            'is_admin' => 1,
        ];

        $this->actingAs($this->user, 'api')
            ->postJson(route('api.users.update', ['user' => $anotherUser->id]), $newUserData)
            ->assertStatus(401);

        $this->assertDatabaseHas('users', $anotherUser->only(['id', 'name', 'surname', 'is_admin']));
    }

    function testUpdateCantChangeLoginToExisting()
    {
        $existingUser = User::factory()->create();
        $newUserData = [
            'login' => $existingUser->login,
            'name' => Str::random(),
            'surname' => Str::random(),
        ];

        $this->actingAs($this->user, 'api')
            ->postJson(route('api.users.update', ['user' => $this->user->id]), $newUserData)
            ->assertJsonValidationErrorFor('login');
    }

    // block

    function testBlockShouldNotBeAvailableForOrdinaryUser()
    {
        $this->actingAs($this->user, 'api')
            ->postJson(route('api.users.toggle-block', ['user' => $this->user->id]), ['block' => 1])
            ->assertStatus(401);

        $this->assertNull($this->user->fresh()->blocked_at);
    }

    function testBlockToggleByAdmin()
    {
        $this->actingAs($this->admin, 'api')
            ->postJson(route('api.users.toggle-block', ['user' => $this->user->id]), ['block' => 1])
            ->assertSuccessful();

        $this->assertNotEmpty($this->user->fresh()->blocked_at);

        $this->actingAs($this->admin, 'api')
            ->postJson(route('api.users.toggle-block', ['user' => $this->user->id]), ['block' => 0])
            ->assertSuccessful();

        $this->assertEmpty($this->user->fresh()->blocked_at);
    }

    // changePassword

    function testChangePasswordForAnotherUserShouldFail()
    {
        $newPasswd = 'Good_password_100';
        $anotherUser = User::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson(
                route('api.users.change-password', ['user' => $anotherUser->id]),
                ['password' => $newPasswd]
            )
            ->assertStatus(401);

        $this->assertEquals($anotherUser->password, $anotherUser->fresh()->password);
    }

    function testChangePasswordWillSucceeded()
    {
        $user = $this->user;
        $this->actingAs($user, 'api')
            ->postJson(
                route('api.users.change-password', ['user' => $user->id]),
                ['password' => 'Another_g00d_passwd']
            )
            ->assertSuccessful();

        $this->assertNotEquals($user->password, $user->fresh()->password);
    }

    function testAdminCanChangePasswordOfAnotherUser()
    {
        $this->actingAs($this->admin, 'api')
            ->postJson(
                route('api.users.change-password', ['user' => $this->user->id]),
                ['password' => 'Another_g00d_passwd']
            )
            ->assertSuccessful();

        $this->assertNotEquals($this->user->password, $this->user->fresh()->password);
    }

    // create

    function testCreateNewUserByAdmin()
    {
        $newUserData = [
            'login' => Str::random(),
            'name' => Str::random(),
            'surname' => Str::random(),
            'password' => 'My_c00L_pass',
            'is_admin' => rand(0, 1),
        ];

        $this->actingAs($this->admin, 'api')
            ->postJson(route('api.users.create'), $newUserData)
            ->assertSuccessful();

        $this->assertDatabaseHas('users', Arr::except($newUserData, ['password']));
    }

    function testCreateNewUserByOrdinaryUserIsUnauthorized()
    {
        $newUserData = [
            'login' => Str::random(),
            'name' => Str::random(),
            'surname' => Str::random(),
            'password' => 'My_c00L_pass',
            'is_admin' => rand(0, 1),
        ];

        $this->actingAs($this->user, 'api')
            ->postJson(route('api.users.create'), $newUserData)
            ->assertStatus(401);

        $this->assertDatabaseMissing('users', Arr::except($newUserData, ['password']));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->admin(false)->blocked(false)->create();
    }
}
