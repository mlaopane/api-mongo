<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Response;

trait NoFilterTrait
{
    /**
     *
     * @param  array    $requestBody
     * @param  Response $response
     *
     * @return Response
     */
    protected function noFilterResponse(array $requestBody, Response $response): Response
    {
        return $response
            ->withStatus(400, "Missing filter")
            ->withJson([
                'error' => "Missing filter key in the request body",
                'requestBody' => $requestBody,
            ]);
    }
}
