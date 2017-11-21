<?php
namespace MykeOn\Middleware;

use Psr\SimpleCache\CacheInterface;
use Slim\Http\{Request, Response};

abstract class CacheMiddleware
{
    public function __construct(CacheInterface $cache, string $subDirectory = '')
    {
        $cache->setSubDirectory($subDirectory);
        $this->cache = $cache;
    }
}
