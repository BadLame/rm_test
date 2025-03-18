<?php

namespace App\Dto;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserUpdateDto
{
    protected function __construct(
        protected string  $name = '',
        protected string  $surname = '',
        protected string  $password = '',
        protected ?bool   $isAdmin = null,
        protected ?Carbon $blockedAt = null,
        protected bool    $updateCredentials = false,
        protected bool    $updatePassword = false,
        protected bool    $toggleBlock = false,
    )
    {
    }

    static function fromArray(array $data): self
    {
        if (!empty($data['name'])) {
            return new self(
                name: $data['name'],
                surname: $data['surname'],
                isAdmin: $data['is_admin'] ?? null,
                updateCredentials: true,
            );
        }
        if (!empty($data['password'])) {
            return new self(
                password: $data['password'],
                updatePassword: true,
            );
        }
        if (isset($data['block'])) {
            return new self(
                blockedAt: !!$data['block'] ? now() : null,
                toggleBlock: true,
            );
        }

        throw new InvalidArgumentException('Data should change name, password or block status');
    }

    function toAttributesArray(): array
    {
        if ($this->updateCredentials) {
            return array_filter([
                'name' => $this->name,
                'surname' => $this->surname,
                'is_admin' => $this->isAdmin,
            ]);
        }

        if ($this->updatePassword) {
            return ['password' => Hash::make($this->password)];
        }

        if ($this->toggleBlock) {
            return ['blocked_at' => $this->blockedAt];
        }

        return [];
    }
}
