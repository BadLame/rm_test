<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $login
 * @property string $password
 */
class LoginRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'login' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:8|max:20',
        ];
    }
}
