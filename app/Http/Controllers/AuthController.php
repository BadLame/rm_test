<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    function login(LoginRequest $request)
    {
        $user = User::query()->firstWhere('login', $request->login);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new AuthenticationException;
        }

        $tokenResult = $user->createToken('Personal Access Token');


        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'expires_at' => $tokenResult->token->expires_at->timestamp,
        ]);
    }

    function logout()
    {
        /** @var User $user */
        $user = auth()->user();
        $user->token()->revoke();

        return response()->noContent();
    }
}
