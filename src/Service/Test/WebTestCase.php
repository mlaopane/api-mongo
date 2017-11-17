<?php
namespace MykeOn\Service\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use MykeOn\Container;

class WebTestCase extends TestCase
{
    /**
     * @param  string $base_uri
     * @return Client
     */
    protected static function createClient($base_uri = 'http://localhost:8000'): Client
    {
        return new Client([
            'base_uri' => $base_uri,
        ]);
    }

    protected static function loadContainer()
    {
        return App::loadContainer();
    }

    /**
     * Get a service from the container
     * @param  string $key
     * @return
     */
    public function get(string $key)
    {
        $container = Container::load();
        if ($container->has($key)) {
            return $container->get($key);
        }
        return null;
    }
}
