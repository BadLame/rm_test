<?php

namespace App\Http\Requests;

use App\Rules\SecurePasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string $surname
 * @property string $password
 * @property bool|null $is_admin
 */
class CreateRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'login' => 'required|string|min:3|max:255|unique:users,login',
            'name' => 'required|string|min:2|max:255',
            'surname' => 'required|string|min:2|max:255',
            'password' => ['required', 'string', 'min:8', 'max:20', new SecurePasswordRule],
            'is_admin' => [
                Rule::excludeIf(!$this->user('api')?->is_admin),
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }
}
