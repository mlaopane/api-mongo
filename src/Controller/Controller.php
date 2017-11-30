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
     * Get a service from the container
     * @param string $key
     * @return null
     */
    public function get(string $key)
    {
        if ($this->container->has($key)) {
            return $this->container->get($key);
        }
        return null;
    }

    public function getDatabase()
    {
        return $this->database;
    }
}
