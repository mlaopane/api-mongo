<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;

class PostController extends HttpController
{
    use ActionTrait;

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
        if (isset($arguments['action'])) {
            return $this->handleActionRequest('database', $request, $response, $arguments);
        }

        $body = $request->getParsedBody();
        return $response->withJson(['message' => 'handleDatabaseRequest']);
    }

    /**
     * handle a POST request for :
     * - creating documents
     * - updating documents by filter
     *
     * @param  Request  $request
     * @param  Response $response
     *
     * @return Response
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        if (isset($arguments['action'])) {
            return $this->handleActionRequest('collection', $request, $response, $arguments);
        }

        $body = $request->getParsedBody();
        return $response->withJson(['message' => 'handleCollectionRequest']);

        // NO ACTION if no data provided
        if (empty($body['data'])) {
            return $response->withStatus(400, "Missing data in the request body");

        // CREATE one or many documents
        } elseif (empty($body['filter'])) {
            if (isset($body['data'][0])) {
                return $this->insertManyWithResponse($response, $body['data']);
            } else {
                return $this->insertOneWithResponse($response, $body['data']);
            }

        // UPDATE one or many documents by filter
        } else {
            return $this->updateManyWithResponse($response, $body['filter'], $body['data']);
        }
    }

    /**
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleDatabaseSearchRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterResponse($requestBody, $response);
        }

        return $response->withJson([
            'databaseName' => $this->databaseName,
            'collections'  => $this->get('db_manager')->fetchDatabaseData($this->database, $requestBody['filter']),
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
    public function handleCollectionSearchRequest(Request $request, Response $response, array $arguments): Response
    {
        $requestBody = $request->getParsedBody();

        // No filter provided
        if (empty($requestBody['filter'])) {
            return $this->noFilterResponse($requestBody, $response);
        }

        return $response->withJson([
            'databaseName'   => $this->databaseName,
            'collectionName' => $this->collectionName,
            'data'           => $this->collection->find($requestBody['filter'])->toArray(),
        ]);
    }

    protected function noFilterResponse(array $requestBody, Response $response): Response
    {
        return $response
            ->withStatus(400, "Missing filter")
            ->withJson([
                'error' => "Missing filter key in the request body",
                'requestBody' => $requestBody,
            ]);
    }

    /**
     * @param  Response $response [description]
     * @param  array    $data     [description]
     *
     * @return Response           [description]
     */
    protected function insertManyWithResponse(Response $response, array $data = []): Response
    {
        // Prevent the client to insert a custom id
        $data = array_map(function ($document) {
            if (key_exists($document['_id'])) {
                unset($document['_id']);
            }
            return $document;
        }, $data);

        $result = $this->collection->insertMany($data);
        $data = [
            "count" => $result->getInsertedCount(),
            "ids" => $result->getInsertedIds(),
        ];
        return $response->withStatus(201, "Created")->withJson($data);
    }

    /**
     * @param  Response $response
     * @param  array    $data
     *
     * @return Response
     */
    protected function insertOneWithResponse(Response $response, array $request_data = []): Response
    {
        // Prevent the client to insert a custom id
        if (key_exists($data['_id'])) {
            unset($data['_id']);
        }
        $result = $this->collection->insertOne($request_data);
        $insert_id = $result->getInsertedId();
        $response_data = array_merge(['id' => $result->getInsertedId()], $request_data);
        return $response
            ->withStatus(201, "Created")
            ->withHeader('Location', $this->fetch('router')->pathFor("{$this->collection}.get", ['id' => $insert_id]))
            ->withJson($response_data);
    }

    /**
     * @param  Response $response
     * @param  array    $filter
     * @param  array    $data
     *
     * @return Response
     */
    protected function updateManyWithResponse(Response $response, array $filter, array $data = []): Response
    {
        $result = $this->collection->updateMany($filter, ['$set' => $data]);
        $body = [
            "matched" => $result->getMatchedCount(),
            "modified" => $result->getModifiedCount(),
        ];
        if ($body['matched'] == 0) {
            return $response->withStatus(200, "No Match")->withJson($body);
        } else {
            return $response->withStatus(200, "Modified")->withJson($body);
        }
    }
}
