<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;

class SearchController extends HttpController
{
    use NoFilterTrait;

    /**
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterResponse($requestBody, $response);
        }

        return $response->withJson([
            'collections' => $this->get('db_manager')->fetchDatabase($this->database, $requestBody['filter']),
        ]);
    }

    /**
     *
     * @param  Request  $request   [description]
     * @param  Response $response  [description]
     * @param  array    $arguments [description]
     *
     * @return Response            [description]
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterResponse($requestBody, $response);
        }

        return $response->withJson([
            'data' => $this->collection->find($requestBody['filter'])->toArray(),
        ]);
    }
}
