<?php

namespace MykeOn\Controller;

use Psr\Container\ContainerInterface;

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
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
}
