<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $search
 */
class SearchRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'search' => 'sometimes|nullable|string',
        ];
    }
}
