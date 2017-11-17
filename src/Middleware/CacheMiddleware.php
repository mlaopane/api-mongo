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

    public function __invoke(Request $request, Response $response, $next)
    {
        $method = 'on'.ucfirst(mb_strtolower($request->getMethod(), 'UTF-8')).'Request';
        return $this->$method($request, $response, $next);
    }
}
