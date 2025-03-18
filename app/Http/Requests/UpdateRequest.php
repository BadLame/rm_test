<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property string $surname
 */
class UpdateRequest extends FormRequest
{
    function rules(): array
    {
        return [
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
