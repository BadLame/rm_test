<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property bool $block
 */
class BlockRequest extends FormRequest
{
    function rules(): array
    {
        return [
            'block' => 'required|boolean',
        ];
    }
}
