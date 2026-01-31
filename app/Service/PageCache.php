<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class PageCache
{
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter(
            namespace: 'wp_page_cache',
            defaultLifetime: 3600,
            directory: WD_BASE_PATH . '/app/Cache'
        );
    }

    public function get(string $key, callable $callback): string
    {
        return $this->cache->get($key, function (ItemInterface $item) use ($callback) {
            $item->tag(['page']);
            return $callback();
        });
    }

    public function invalidate(string $key): void
    {
        $this->cache->deleteItem($key);
    }

    public function clearAll(): void
    {
        $this->cache->clear();
    }
}
