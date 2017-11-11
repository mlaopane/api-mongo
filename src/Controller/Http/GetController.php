<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;
use MongoDB\Database;

class GetController extends HttpController
{
    /**
     * handle a request on a database level (based on the URI arguments)
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        $responseBody = [
            'databaseName' => $this->databaseName,
            'collections' => $this->fetchCollectionsData($this->database),
        ];

        if (empty($responseBody['collections'])) {
            $response->withStatus(200, 'No data found');
        }
        return $response->withJson($responseBody, 200);
    }

    /**
     * handle a request on a collection level (based on the URI arguments)
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleCollectionRequest(Request $request, Response $response, array $args): Response
    {
        $responseBody = [
            'databaseName' => $this->databaseName,
            'collectionName' => $this->collectionName,
        ];

        // Get a document by its id
        if (!empty($args["id"])) {
            $responseBody['data'] = $this->collection->findOne(['_id' => new ObjectId($args["id"])]);
        // Get one or many documents by filter
        } else {
            $responseBody['data'] = $this->collection->find()->toArray();
        }

        if (empty($responseBody['data'])) {
            $response->withStatus(200, 'No data found');
        }
        return $response->withJson($responseBody);
    }

    /**
     * Fetch documents from all the collections of the provided database
     * @param  \MongoDB\Database $database
     * @return array
     */
    protected function fetchCollectionsData(\MongoDB\Database $database): array
    {
        $collectionIterator = $database->listCollections();
        $collectionIterator->rewind();
        $collections = [];

        // Grab all documents from the database's collections
        while ($collectionIterator->valid()) {
            $collectionName = $collectionIterator->current()->getName();
            // Add a collection to the response body
            if (!empty($data = $this->database->$collectionName->find()->toArray())) {
                $collections[] = [
                    'name' => $collectionName,
                    'data' => $data,
                ];
            }
            $collectionIterator->next();
        }

        return $collections;
    }
}
