<?php
namespace MykeOn\Controller\Http;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Http\Message\UriInterface;
use MykeOn\Controller\Controller;

/**
 *
 */
abstract class HttpController
{
    /**
     * @var string
     */
    protected $databaseName;

    /**
     * @var \MongoDB\Database
     */
    protected $database;

    /**
    * @var string
    */
    protected $collectionName;

    /**
     * @var \MongoDB\Collection
     */
    protected $collection;

    /* -------------------- */

    /**
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    public function handleRequest(Request $request, Response $response, array $arguments): Response
    {
        $this->setDatabaseData($arguments);

        // Returns a 'bad request' response if no collection is provided for a POST | PUT request
        if (empty($arguments['collection'])) {
            if ($request->isPost() || $request->isPut()) {
                return $response->withStatus(400, "POST | PUT not allowed without collection");
            }
        } else {
            $this->setCollectionData($arguments);
            return $this->handleCollectionRequest($request, $response, $arguments);
        }

        return $this->handleDatabaseRequest($request, $response, $arguments);
    }

    /* -------------------- */

    /**
     * Initialize $this->databaseName and $this->database
     * @param  array $arguments
     * @return this
     */
    protected function setDatabaseData(array $arguments)
    {
        $client = new \MongoDB\Client;
        $this->databaseName = $arguments['database'];
        $this->database = $client->selectDatabase($arguments['database']);

        return $this;
    }

    /**
     * Initialize $this->collectionName and $this->collection
     * @param  array $arguments
     * @return this
     */
    protected function setCollectionData(array $arguments)
    {
        $this->collectionName = $arguments['collection'];
        $this->collection = $this->database->selectCollection($arguments['collection']);

        return $this;
    }

    /* -------------------- */

    /**
     * handle a request on a database level (based on the URI arguments)
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    abstract public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response;

    /**
     * handle a request on a collection level (based on the URI arguments)
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $arguments
     *
     * @return Response
     */
    abstract public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response;
}
