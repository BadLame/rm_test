<?php

namespace App\Http\Requests;

use App\Rules\SecurePasswordRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $password
 */
class ChangePasswordRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'max:20', new SecurePasswordRule],
        ];
    }
}
