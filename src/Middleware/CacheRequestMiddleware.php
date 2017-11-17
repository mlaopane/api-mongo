<?php
namespace MykeOn\Middleware;

use Psr\SimpleCache\CacheInterface;
use Slim\Http\{Request, Response};
use MykeOn\Helper\String\CacheKey;

class CacheRequestMiddleware extends CacheMiddleware
{
    public function __construct(CacheInterface $cache)
    {
        parent::__construct($cache, 'request');
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
    protected function onGetRequest(Request $request, Response $response, callable $next)
    {
        $cacheKey = (new CacheKey())->useRequest($request);
        // Get the data from the cache
        if ($this->cache->has((string) $cacheKey)) {
            return $response->withJson($this->cache->get($cacheKey), 200);
        // Get the data form the database then save it in the cache
        } else {
            $response = $next($request, $response);
            $responseBody = $response->getBody();
            $responseBody->rewind();
            // Add to the cache
            $this->cache->set($cacheKey, $responseBody->getContents());
            return $response;
        }
    }

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  callable $next
     *
     * @return Response
     */
    protected function onPostRequest(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);

        if (!$this->isSearchRequest($request)) {
            $statusCode = (string) $response->getStatusCode();
            // Clear the cache if the status is OK
            if ($statusCode[0] === '2') {
                $this->cache->clear();
            }
        }

        return $response;
    }

    protected function onPutRequest(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);

        // Clear the cache if the document has been successfully replaced
        if ($response->getStatusCode() == 200) {
            $this->cache->clear();
        }

        return $response;
    }

    protected function onPatchRequest(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);

        return $response;
    }

    protected function onDeleteRequest(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);
        $cacheKey = (new CacheKey())->useRequest($request);
        $keys = $this->cache->getKeys($cacheKey->__toString());

        if ($response->getStatusCode() == 200) {
            $this->cache->deleteMultiple($keys);
        }

        return $response;
    }

    /**
     * @param  Request $request
     * @return bool
     */
    protected function isSearchRequest(Request $request): bool
    {
        return (bool) strpos('_search', $request->getUri()->getPath());
    }
}
