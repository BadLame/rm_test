<?php

namespace Tests\Unit\Rules;

use App\Rules\SecurePasswordRule;
use PHPUnit\Framework\TestCase;

class SecurePasswordRuleTest extends TestCase
{
    function testValidate(): void
    {
        $rule = new SecurePasswordRule;
        $passwords = [
            'upper' => 'without_uppercase_test_n1',
            'number' => 'withOut_number_test',
            'char' => 'WithoutSpecialCharTestN1',
            'success' => 'Good_password_which_should_pass_100%',
        ];

        // Проходимся по каждому паролю и заменяем его значение на 'passed', если $fail был вызван
        foreach ($passwords as $key => $value) {
            $rule->validate($key, $value, function (string $_message) use (&$passwords, $key) {
                $passwords[$key] = 'passed';
            });
        }

        // Убираем из массива все 'passed'
        $passwords = array_filter($passwords, fn (string $password) => $password !== 'passed');

        // Проверяем, что в массиве остался только один ключ - success
        $this->assertCount(1, $passwords);
        $this->assertTrue(!empty($passwords['success']));
    }
}
