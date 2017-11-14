<?php
namespace MykeOn\Middleware;

use Psr\SimpleCache\CacheInterface;
use MykeOn\Service\Cache\CacheKey;
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
        return $this->$method($request, $response, $next);
    }

    /**
     * Executre cache instructions on a GET request
     *
     * @param  CacheKey  $cacheKey
     * @param  Request   $request
     * @param  Response  $response
     * @param  callable  $next
     *
     * @return Response
     */
    protected function onGetRequest(
        Request $request,
        Response $response,
        callable $next
    ): Response {
        $cacheKey = new CacheKey($request);
        // Get the data from the cache
        if ($this->cache->has((string) $cacheKey)) {
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
