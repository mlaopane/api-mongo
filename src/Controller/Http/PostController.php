<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

class PostController extends HttpController
{
    /**
     * handle a POST request for creating documents on a given database
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        /* NOT ALLOWED */
        return null;
    }

    /**
     * handle a POST request for creating documents on a given collection
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
        } else {
            return
                empty($requestBody['data'][0]) ?
                $this->insertOneWithResponse($response, $requestBody['data']) :
                $this->insertManyWithResponse($response, $requestBody['data']);
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
                'count' => count($documents),
                'data'  => $documents,
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
        // if (isset($document['_id'])) {
        //     unset($document['_id']);
        // }

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
                'data' => $document
            ]);
    }

}
