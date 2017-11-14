<?php
namespace MykeOn\Middleware;

use Psr\SimpleCache\CacheInterface;
use Slim\Http\{Request, Response};

class CacheMiddleware
{
    public function __construct(CacheInterface $cache, string $subDir = '')
    {
        $cache->addSubDir($subDir);
        $this->cache = $cache;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $method = 'on'.ucfirst(mb_strtolower($request->getMethod(), 'UTF-8')).'Request';
        $cacheKey = str_replace('/', '_', substr($request->getUri()->getPath(), 1));
        return $this->$method($cacheKey, $request, $response, $next);
    }

    /**
     * [onGetRequest description]
     * @param  [type]   $cacheKey
     * @param  Request  $request
     * @param  Response $response
     * @param  [type]   $next
     * @return [type]
     */
    protected function onGetRequest($cacheKey, Request $request, Response $response, $next)
    {
        // Get the data from the cache
        if ($this->cache->has($cacheKey)) {
            return $response->withJson($this->cache->get($cacheKey), 200);
        // Get the data form the database then save it in the cache
        } else {
            $response = $next($request, $response);
            $responseBody = $response->getBody();
            $responseBody->rewind();
            $this->cache->set($cacheKey, $responseBody->getContents());
            return $response;
        }
    }
}
