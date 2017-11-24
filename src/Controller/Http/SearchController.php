<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

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

        if (!empty($requestBody['filter']['id'])) {
            $id = $requestBody['filter']['id'];
            $requestBody['filter'] = $this->createIdFilter($id);
            if (null === $requestBody['filter']) {
              return $response
                ->withStatus(400, 'Bad Request')
                ->withJson(['error' => 'id should be a string or an array of strings']);
            }
        }

        // No data found
        if (empty($data = $this->collection->find($requestBody['filter'])->toArray())) {
            return $response
                ->withStatus(200, 'No data found')
                ->withJson(['data' => []]);
        }

        return $response->withJson([
            'data' => $data,
        ]);
    }

    public function createIdFilter($id)
    {
      if (is_string($id)) {
          return ['_id' => new ObjectId($id)];
      }
      if (is_array($id)) {
          return [
              '_id' => [
                  '$in' => array_map(function ($id) {
                      return new ObjectId($id);
                  }, $id)
              ]
          ];
      }
      return null;
    }
}
