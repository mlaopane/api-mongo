<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\Driver\Exception\InvalidArgumentException;

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
        extract($request->getParsedBody()); // filter

        // No filter provided
        if (empty($filter)) {
            return $this->noFilterProvidedResponse($response, ['filter' => $filter]);
        }

        // Filter by id
        if (!empty($filter['id'])) {
            $filter = $this->createIdFilter($filter['id']);
            if (null === $filter) {
                return $response
                    ->withStatus(400, 'Bad Request')
                    ->withJson(['error' => 'id should be a string or an array of strings']);
            }
        }

        // No data found
        if (empty($data = $this->collection->find($filter)->toArray())) {
            return $response
                ->withStatus(200, 'No data found')
                ->withJson([$this->collectionName => []]);
        }

        return $response->withJson([$this->collectionName => $data]);
    }

    /**
     * @param string $idParam
     * @return array|null
     */
    public function createIdFilter($idParam)
    {
        if (is_string($idParam)) {
            return ['_id' => $idParam];
        }
        if (is_array($idParam)) {
            $ids = array_map(function ($id) {
                return new $id;
            }, $idParam);
            return [
                '_id' => [
                    '$in' => $ids
               ]
            ];
      }
      return null;
    }
}
