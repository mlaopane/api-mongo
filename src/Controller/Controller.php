<?php

namespace MykeOn\Controller;

use Psr\Container\ContainerInterface;
use MongoDB\Client;
use MongoDB\Database;

/**
 *
 */
abstract class Controller
{
    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Database
     */
    protected $database;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $client = new Client();
        $this->database = $client->selectDatabase('cvtheque');
    }

    /**
     * Fetch a service from the container
     * @param  string $key
     */
    public function fetch(string $key)
    {
        if (!$this->container->has($key)) {
            return null;
        }
        return $this->container->get($key);
    }

    public function getDatabase()
    {
        return $this->database;
    }
}
