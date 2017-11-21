<?php
namespace MykeOn\Middleware;

use Slim\Http\{Request, Response};

interface CacheRequestInterface
{
    /**
     * Execute cache instructions on a GET request
     *
     * @param  Request   $request
     * @param  Response  $response
     * @param  callable  $next
     *
     * @return Response
     */
    public function onGetRequest(Request $request, Response $response, callable $next);

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  callable $next
     *
     * @return Response
     */
    public function onPostRequest(Request $request, Response $response, callable $next): Response;

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  callable $next
     *
     * @return Response
     */
    public function onPutRequest(Request $request, Response $response, callable $next): Response;

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  callable $next
     *
     * @return Response
     */
    public function onPatchRequest(Request $request, Response $response, callable $next): Response;

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  callable $next
     *
     * @return Response
     */
    public function onDeleteRequest(Request $request, Response $response, callable $next): Response;

}
