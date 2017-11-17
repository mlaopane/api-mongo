<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 *
 */
trait ErrorResponseTrait
{
    /**
     * @param  Response $response
     * @param  array    $body
     *
     * @return Response
     */
    protected function noFilterProvidedResponse(Response $response, array $body): Response
    {
        return $response
            ->withStatus(400, "Missing filter")
            ->withJson([
                'error' => "Missing filter in the request body",
                'found'  => array_keys($body),
            ]);
    }

    /**
     * @param  Response $response
     * @param  array    $body
     *
     * @return Response
     */
    protected function noDataProvidedResponse(Response $response, array $body): Response
    {
        return $response
            ->withStatus(400, "Missing data")
            ->withJson([
                'error' => "Missing data in the request body",
                'found' => array_keys($body),
            ]);
    }
}
