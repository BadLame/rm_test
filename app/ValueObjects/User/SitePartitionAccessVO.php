<?php

namespace App\ValueObjects\User;

use App\Models\Enums\User\SiteModulesAccessEnum;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class SitePartitionAccessVO implements Arrayable, JsonSerializable
{
    protected array $access;

    function __construct(?string $access = null, bool $forAdmin = false)
    {
        $modules = array_column(SiteModulesAccessEnum::cases(), 'value');

        $this->access = array_fill_keys($modules, $forAdmin);

        if ($access && !$forAdmin) {
            $this->access = array_merge($this->access, json_decode($access, true));
        }
    }

    function set(SiteModulesAccessEnum $module, bool $isAccessible): self
    {
        $this->access[$module->value] = $isAccessible;
        return $this;
    }

    function get(SiteModulesAccessEnum $module): bool
    {
        return $this->access[$module->value] ?? false;
    }

    function toArray(): array
    {
        return $this->access;
    }

    function jsonSerialize(): string
    {
        return json_encode($this->access);
    }
}
