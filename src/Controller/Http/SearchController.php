<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;

class SearchController extends HttpController
{
    /**
     * {@inheritdoc}
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterProvidedResponse($response, $requestBody);
        }

        return $response->withJson([
            'collections' => $this->get('db_manager')->fetchDatabase($this->database, $requestBody['filter']),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterProvidedResponse($response, $requestBody);
        }

        // No data found
        if (empty($data = $this->collection->find($requestBody['filter'])->toArray())) {
            return $response->withStatus(204, 'No data found');
        }

        return $response->withJson([
            'data' => $data,
        ]);
    }
}
