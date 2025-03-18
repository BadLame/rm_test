<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    function testLoginUserWithValidCredentialsCreatesAuthToken(): void
    {
        $user = User::factory()->blocked(false)->create();
        $authData = [
            'login' => $user->login,
            'password' => 'password',
        ];

        $this->postJson(route('api.auth.login'), $authData)
            ->assertSuccessful()
            ->assertJsonStructure(['access_token', 'expires_at']);

        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $user->id,
        ]);
    }

    function testLoginUserWithInvalidCredentialsThrowsAuthException(): void
    {
        $user = User::factory()->blocked(false)->create();
        $authData = [
            'login' => $user->login,
            'password' => Str::random(),
        ];

        $this->postJson(route('api.auth.login'), $authData)
            ->assertStatus(401);
    }

    function testLogoutRevokesToken()
    {
        $user = User::factory()->blocked(false)->create();

        // Залогиниться, чтобы у пользователя появился токен
        $authToken = $this->postJson(route('api.auth.login'), ['login' => $user->login, 'password' => 'password'])
            ->json('access_token');

        $this->postJson(route('api.auth.logout'), headers: ['Authorization' => "Bearer $authToken"])
            ->assertSuccessful();

        $this->assertDatabaseMissing('oauth_access_tokens', [
            'user_id' => $user->id,
            'revoked' => false,
        ]);
    }
}
