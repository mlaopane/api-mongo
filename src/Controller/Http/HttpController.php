<?php
namespace MykeOn\Controller\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\UriInterface;
use Psr\Cache\CacheInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use MykeOn\Controller\Controller;
use MykeOn\Controller\Http\ErrorResponseTrait;

/**
 *
 */
abstract class HttpController extends Controller
{
    use ErrorResponseTrait;
    
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

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey;

    /* -------------------- */

    /**
     * @param ContainerInterface $container [description]
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->cache = $container['cache'];
    }

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

        if (empty($arguments['collection'])) {
            return $this->handleDatabaseRequest($request, $response, $arguments);
        }

        $this->setCollectionData($arguments);
        return $this->handleCollectionRequest($request, $response, $arguments);
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
     * Initialize the current collection
     *
     * @param  array $arguments
     *
     * @return self
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
