<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

class ReplaceController extends HttpController
{
    /**
     * {@inheritDoc}
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $args): Response
    {
        /** NOT ALLOWED **/
        return null;
    }

    /**
     * handle a PUT request to replace a document
     *
     * {@inheritDoc}
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        extract($arguments); // id
        extract($request->getParsedBody()); // data

        // If no data provided
        if (empty($data)) {
            return $response->withStatus(400, "Missing data from the request body");
        }

        // Update one document by id
        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => $data]
        );

        if ($result->getModifiedCount() === 0) {
            return $response->withStatus(204, "No Modification");
        } else {
            return $response->withStatus(200, "Modified");
        }
    }

}
