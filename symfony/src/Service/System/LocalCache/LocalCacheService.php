<?php

declare(strict_types=1);

namespace App\Service\System\LocalCache;

class LocalCacheService
{
    const ADMIN_IN_PUBLIC = 'ADMIN_IN_PUBLIC';

    /**
     * @var array
     */
    public $cache = [];

    public function set(string $name, $value): void
    {
        $this->cache[$name] = $value;
    }

    /**
     * @return mixed|null
     */
    public function get(string $name)
    {
        return $this->cache[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->cache[$name]);
    }

    public function remove(string $name): void
    {
        unset($this->cache[$name]);
    }

    public function clearAll(): void
    {
        $this->cache = [];
    }
}
