<?php

namespace App\ValueObjects\User;

use App\Models\Enums\User\SitePartitionAccessEnum;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class SitePartitionAccessVO implements Arrayable, JsonSerializable
{
    protected array $access;

    function __construct(?string $access = null, bool $forAdmin = false)
    {
        $partitions = array_column(SitePartitionAccessEnum::cases(), 'value');

        $this->access = array_fill_keys($partitions, $forAdmin);

        if ($access && !$forAdmin) {
            $this->access = array_merge($this->access, json_decode($access, true));
        }
    }

    function set(SitePartitionAccessEnum $partition, bool $isAccessible): self
    {
        $this->access[$partition->value] = $isAccessible;
        return $this;
    }

    function get(SitePartitionAccessEnum $partition): bool
    {
        return $this->access[$partition->value] ?? false;
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
