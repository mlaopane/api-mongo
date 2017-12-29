<?php
namespace MykeOn\Middleware;

use Psr\SimpleCache\CacheInterface;
use Slim\Http\{Request, Response};
use MykeOn\Helper\String\CacheKey;

class CacheRequestMiddleware extends CacheMiddleware implements CacheRequestInterface
{
    public function __construct(CacheInterface $cache)
    {
        // Sets the current cache subdirectory
        parent::__construct($cache, 'request');
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $verb = mb_strtolower($request->getMethod(), 'UTF-8');
        $method = 'on'.ucfirst($verb).'Request';
        if (method_exists($this, $method)) {
            return $this->$method($request, $response, $next);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onGetRequest(Request $request, Response $response, callable $next): Response
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
     * {@inheritDoc}
     */
    public function onPostRequest(Request $request, Response $response, callable $next): Response
    {
        $response = $next($request, $response);

        /* Clear the cache if the SEARCH request was successful */
        if (!$this->isSearchRequest($request)) {
            $statusCode = (int) $response->getStatusCode();
            if ($statusCode === 201) { // CREATION
                $this->cache->clear();
            }
        }
        /* ----- */

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function onPutRequest(Request $request, Response $response, callable $next): Response
    {
        $response = $next($request, $response);

        /* Invalidate the cache if the PUT request was successful */
        $statusCode = (int) $response->getStatusCode();
        if ($statusCode == 201 || $statusCode == 200) { // REPLACE (or CREATE)
            $this->cache->clear();
        }
        /* ----- */

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function onPatchRequest(Request $request, Response $response, callable $next): Response
    {
        $response = $next($request, $response);

        /* Invalidate the cache if the PATCH request was successful */
        $statusCode = (int) $response->getStatusCode();
        if ($statusCode === 200) {
            $cacheKey = (new CacheKey())->useRequest($request);
            $keys = $this->cache->getKeys($cacheKey);
            $this->cache->deleteMultiple($keys);
        }
        /* ----- */

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function onDeleteRequest(Request $request, Response $response, callable $next): Response
    {
        $response = $next($request, $response);

        /* Invalidate the cache if the DELETE request was successful */
        $statusCode = (int) $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            $cacheKey = (new CacheKey())->useRequest($request);
            $keys = $this->cache->getKeys($cacheKey);
            $this->cache->deleteMultiple($keys);
        }
        /* ----- */

        return $response;
    }

    /**
     * @param  Request $request
     *
     * @return bool
     */
    protected function isSearchRequest(Request $request): bool
    {
        return (bool) strpos('_search', $request->getUri()->getPath());
    }
}
