<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $user // Параметр роута
 *
 * @property string $login
 * @property string $name
 * @property string $surname
 * @property bool|null $is_admin
 */
class UpdateRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('users', 'login')->ignore($this->user),
            ],
            'name' => 'required|string|min:2|max:255',
            'surname' => 'required|string|max:255',
            'is_admin' => [
                Rule::excludeIf(!$this->user('api')?->is_admin),
                'sometimes',
                'bool',
            ],
        ];
    }
}
