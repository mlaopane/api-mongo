<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

class PostController extends HttpController
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
        return null;
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
        $requestBody = $request->getParsedBody();

        // NO ACTION if no data provided
        if (empty($requestBody['data'])) {
            return $response->withStatus(400, "Missing data in the request body");

        // CREATE one or many documents
        } elseif (empty($requestBody['filter'])) {
            return empty($requestBody['data'][0]) ?
                $this->insertOneWithResponse($response, $requestBody['data']) :
                $this->insertManyWithResponse($response, $requestBody['data']);

        // UPDATE one or many documents by filter
        } else {
            return $this->updateManyWithResponse($response, $body['filter'], $body['data']);
        }
    }

    /**
     * @param  Response $response [description]
     * @param  array    $data     [description]
     *
     * @return Response           [description]
     */
    protected function insertManyWithResponse(Response $response, array $documents = []): Response
    {
        // Update the documents with the generated object id
        $documents = array_map(function ($document) {
            // Prevent the client from inserting a custom _id
            if (isset($document['_id'])) {
                unset($document['_id']);
            }
            $document['id'] = $this->collection->insertOne($document)->getInsertedId();
            return $document;

        }, $documents);

        return $response
            ->withStatus(201, "Created")
            ->withJson([
                'databaseName'   => $this->databaseName,
                'collectionName' => $this->collectionName,
                'count'          => count($documents),
                'data'           => $documents,
            ]);
    }

    /**
     * @param  Response $response
     * @param  array    $data
     *
     * @return Response
     */
    protected function insertOneWithResponse(Response $response, array $document = []): Response
    {
        // Prevent the client to insert a custom id
        if (isset($document['_id'])) {
            unset($document['_id']);
        }

        $result = $this->collection->insertOne($document);
        $insertedId = $result->getInsertedId();
        $document['id'] = $insertedId;

        return $response
            ->withStatus(201, "Created")
            ->withHeader('Location', $this->get('router')->pathFor('get', [
                'database'   => $this->databaseName,
                'collection' => $this->collectionName,
                'id'         => $insertedId,
            ]))
            ->withJson([
                'databaseName'   => $this->databaseName,
                'collectionName' => $this->collectionName,
                'data'           => $document
            ]);
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
