<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurePasswordRule implements ValidationRule
{
    function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string)$value;

        $hasUppercaseLetter = preg_match('/[A-Z]/', $value);
        $hasNumber = preg_match('/[0-9]/', $value);
        $hasSpecialCharacter = preg_match('/[^A-Za-z0-9 ]/', $value);

        if (!$hasUppercaseLetter) {
            $fail('The :attribute must have uppercase letter');
        } elseif (!$hasNumber) {
            $fail('The :attribute must have number');
        } elseif (!$hasSpecialCharacter) {
            $fail('The :attribute must have special character');
        }
    }
}
