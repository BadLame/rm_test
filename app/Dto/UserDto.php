<?php

namespace App\Dto;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserDto
{
    protected function __construct(
        protected string  $name = '',
        protected string  $surname = '',
        protected string  $password = '',
        protected string  $login = '',
        protected ?bool   $isAdmin = null,
        protected ?Carbon $blockedAt = null,
        protected bool    $saveCredentials = false,
        protected bool    $savePassword = false,
        protected bool    $toggleBlock = false,
    )
    {
    }

    static function fromArray(array $data): self
    {
        if (isset($data['block'])) {
            return new self(
                blockedAt: !!$data['block'] ? now() : null,
                toggleBlock: true,
            );
        }

        if (!empty($data['login']) || !empty($data['password'])) {
            $properties = [];

            if (!empty($data['login'])) {
                $properties = array_merge($properties, [
                    'login' => $data['login'],
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'isAdmin' => $data['is_admin'] ?? null,
                    'saveCredentials' => true,
                ]);
            }
            if (!empty($data['password'])) {
                $properties = array_merge($properties, [
                    'password' => $data['password'],
                    'savePassword' => true,
                ]);
            }

            return new self(...$properties);
        }

        throw new InvalidArgumentException('Data should change name, password or block status');
    }

    function toAttributesArray(): array
    {
        if ($this->toggleBlock) {
            return ['blocked_at' => $this->blockedAt];
        }

        $attributes = [];

        if ($this->saveCredentials) {
            $attributes = array_merge($attributes, [
                'login' => $this->login,
                'name' => $this->name,
                'surname' => $this->surname,
                'is_admin' => $this->isAdmin,
            ]);
        }

        if ($this->savePassword) {
            $attributes = array_merge($attributes, ['password' => $this->password ? Hash::make($this->password) : null]);
        }

        return array_filter($attributes, fn (mixed $value) => !is_null($value));
    }
}
