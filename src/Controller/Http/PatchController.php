<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

class PatchController extends HttpController
{
    private $actions = [
        'set'   => '$set',
        'unset' => '$unset',
    ];

    /**
     * handle a PATCH request for updating documents on a given database
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
     * handle a PATCH request for updating documents on a given collection
     *
     * @param  Request  $request
     * @param  Response $response
     *
     * @return Response
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        extract($arguments); // action, id
        extract($request->getParsedBody()); // filter, data

        //
        if (empty($this->actions[$action])) {
            return $response->withStatus(400, "Unknown action");
        }

        // NO ACTION if no data provided
        if (empty($data)) {
            return $response->withStatus(400, "Missing data in the request body");
        }

        // UPDATE by id if provided or by filter if provided
        if (!empty($id)) {
            return $this->updateWithResponse($response, $data, ['_id' => new ObjectId($id)], $this->actions[$action]);
        } else {
            return $this->updateWithResponse($response, $data, $requestBody['filter'] ?? [], $this->actions[$action]);
        }
    }

    /**
     * @param  Response $response
     * @param  array    $data
     * @param  array    $filter   If the filter is empty => all the documents in the collection will be updated
     * @param  string   $action
     *
     * @return Response
     */
    protected function updateWithResponse(Response $response, array $data, array $filter, $action): Response
    {
        $result = $this->collection->updateMany($filter, [$action => $data]);
        $matched = $result->getMatchedCount();
        $modified = $result->getModifiedCount();

        if ($matched === 0) {
            return $response->withStatus(204, "No Match");
        } elseif ($modified === 0) {
            return $response->withStatus(204, "No modification");
        } else {
            return $response->withStatus(200, "Modified")->withJson([
                'message' => 'Modification done',
                'matched' => $matched,
                'modified' => $modified,
            ]);
        }
    }
}
