<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;
use MongoDB\DeleteResult;
use MongoDB\Driver\Exception\BulkWriteException;

class DeleteController extends HttpController
{
    /**
     * handle a DELETE request on a database level
     *
     * {@inheritDoc}
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        extract($request->getParsedBody());

        // Drop database if no filter
        if (empty($filter)) {

            return $response->withStatus(200, 'Deleted')->withJson($this->database->drop())-
        // Drop collections by filter
        } else {
            return $this->deleteCollectionsWithResponse($response, $filter);
        }

        return $response->withJson($responseBody);
    }

    /**
     * handle a DELETE request on a collection level
     *
     * {@inheritDoc}
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        extract($arguments); // id
        extract($request->getParsedBody());

        // Delete one document if the ID is provided
        if (!empty($id)) {
            return $this->deleteOneWithResponse($response, $id);
        // Drop the collection if no argument or body is provided
        } elseif (empty($filter)) {
            if ($this->collection->drop()->ok) {
                return $response->withStatus(204, 'Deleted');
            } else {
                return $response->withStatus(204, 'No action');
            }
        }
        // Delete one or many documents if there's no ID but some filter
        return $this->deleteManyWithResponse($response, $filter);
    }

    /**
     * @param  Response  $response
     * @param  array     $filter
     *
     * @return Response
     */
    protected function deleteCollectionsWithResponse(Response $response, array $filter): Response
    {
        $deleted = 0;

        $collectionIterator = $this->database->listCollections();
        $collectionIterator->rewind();

        while ($collectionIterator->valid()) {
            $collectionName = $collectionIterator->current()->getName();
            $deleted += $this->database->$collectionName->deleteMany($filter)->getDeletedCount();
            $collectionIterator->next();
        }

        if ($responseBody == 0) {
            return $response->withStatus(204, 'No match | Not deleted');
        } else {
            return $response->withStatus(200, 'Deleted')->withJson(['deleted' => $count]);
        }
    }

    /**
     * @param  Response $response
     * @param  string   $id
     *
     * @return Response
     */
    protected function deleteOneWithResponse(Response $response, string $id): Response
    {
        try {
            $result = $this->collection->deleteOne(["_id" => new ObjectId($id)]);
        } catch (BulkWriteException $e) {
            return $response->withStatus(400, 'Bad request')->withJson(['error' => $e->getMessage()]);
        }
        //
        if (!$result->isAcknowledged()) {
            return $response->withStatus(500);
        //
        } elseif ($result->getDeletedCount() === 0) {
            return $response
            ->withStatus(404, 'Not found')
            ->withJson(['error' => 'The resource doesn\'t exists']);
        }

        return $response->withStatus(204, 'Deleted');
    }

    /**
     * @param  Response $response
     * @param  array    $filter
     *
     * @return Response
     */
    protected function deleteManyWithResponse(Response $response, array $filter): Response
    {
        try {
            $count = $this->collection
                ->deleteMany($filter)
                ->getDeletedCount();
        } catch (BulkWriteException $e) {
            return $response->withStatus(400, 'Bad request')->withJson(['error' => $e->getMessage()]);
        }

        if ($count == 0) {
            return $response->withStatus(204, 'No match | Not deleted');
        } else {
            return $response->withStatus(200, 'Deleted')->withJson(['deleted' => $count]);
        }

    }

}
