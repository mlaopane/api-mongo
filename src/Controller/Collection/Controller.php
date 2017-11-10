<?php

namespace CVtheque\Controller\Collection;

use Psr\Container\ContainerInterface;
use MongoDB\Collection;
use Slim\Http\Request;
use Slim\Http\Response;
use CVtheque\Controller\Controller as BaseController;

/**
 *
 */
abstract class Controller extends BaseController
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->initCollection();
    }

    /**
     *
     * @return Controller
     */
    protected function initCollection(): Controller
    {
        $search = [__NAMESPACE__, "\\", "Controller"];
        $collectionName = mb_strtolower(
            str_replace($search, "", static::class),
            'utf-8'
        );
        $this->collection = $this->database->$collectionName;

        return $this;
    }

    /**
     * handle a GET request on the skill collection
     * by args or filter contained in the request body
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $args
     *
     * @return Response
     */
    public function get(Request $request, Response $response, array $args): Response
    {
        $data = $this->collection->find()->toArray();

        return $response->withJson($data);
    }

    /**
     * handle a GET request with multiple arguments
     * by args or filter contained in the request body
     *
     * @param  Request  $request  [description]
     * @param  Response $response [description]
     * @param  array    $args     [description]
     *
     * @return Response           [description]
     */
    public function getBy(Request $request, Response $response, array $args): Response
    {
        return $response->withJson($this->collection->find($args)->toArray());
    }

    /**
     * handle a POST request for creation
     *
     * @param  Request  $request  [description]
     * @param  Response $response [description]
     * @param  array    $args     [description]
     *
     * @return Response           [description]
     */
    public function post(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $response = $response->withHeader('Content-Type', 'application/json');

        // NO ACTION if no data provided
        if (empty($body['data'])) {
            return $response->withStatus(400, "Missing data in the request body");

        // CREATE one or many documents
        } elseif (empty($body['filter'])) {
            if (is_array($body['data'])) {
                $result = $this->collection->insertMany($body['data']);
                $data = [
                    "count" => $result->getInsertedCount(),
                    "ids" => $result->getInsertedIds(),
                ];
            } else {
                $result = $this->collection->insertOne($body['data']);
                $data = [
                    "count" => 1,
                    "id" => $result->getInsertedId(),
                ];
            }
            return $response->withStatus(201, "Created")->withJson($data);

        // UPDATE one or many documents by filter
        } else {
            $result = $this->collection->updateMany(
                $body['filter'],
                ['$set' => $body['data']]
            );
            $data = [
                "matched" => $result->getMatchedCount(),
                "modified" => $result->getModifiedCount(),
            ];
            if ($data['modified'] == 0) {
                return $response->withStatus(200, "No Match")->withJson($data);
            } else {
                return $response->withStatus(200, "Modified")->withJson($data);
            }
        }
    }

    /**
     * handle a PUT request for update or create a document with a provided id
     * by args or a filter contained in the request body
     *
     * @param  Request  $request  [description]
     * @param  Response $response [description]
     * @param  array    $args     [description]
     *
     * @return Response           [description]
     */
    public function put(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $response = $response->withHeader('Content-Type', 'application/json');

        // If no data provided
        if (empty($body['data'])) {
            return $response->withStatus(400, "Missing data from the request body");
        }

        // Update one document by id
        $result = $this->collection->updateOne(
            ['id' => $args['id']],
            ['$set' => $body['data']],
            ['upsert' => true]
        );

        // Modification
        if ($result->getModifiedCount() === 1) {
            return $response->withStatus(200, "Modified");
        // Creation
        } elseif ($result->getUpsertedCount() === 1) {
            return $response->withStatus(201, "Created");
        // No action when no difference
        } else {
            return $response->withStatus(204, "No difference");
        }
    }

    /**
     * handle a DELETE request
     * by args or a filter contained in the request body
     *
     * @param  Request  $request  [description]
     * @param  Response $response [description]
     * @param  array    $args     [description]
     *
     * @return Response           [description]
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $data = [
            "count" => 0,
        ];

        // Drop the database if no argument or body is provided
        if (empty($args) && empty($body['filter'])) {
            $data = $this->collection->drop();

        // Delete one document if the ID is provided
        } elseif (isset($args["id"])) {
            $this->collection->deleteOne(["id" => $args["id"]]);
            $data['count'] = 1;

        // Delete one or many documents if there's no ID but some filter
        } else {
            $data['count'] = $this->collection
                ->deleteMany($body['filter'])
                ->getDeletedCount();
        }

        return $response->withJson($data);
    }
}
