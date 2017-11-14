<?php
namespace MykeOn;

use Psr\Container\ContainerInterface;

define('ROOT_DIR', dirname(__DIR__));
define('CONFIG_DIR', ROOT_DIR.'/app/config');

/**
 *
 */
class App
{
    private static $container;

    /**
     * Entry point for the API
     */
    public static function launch()
    {
        $app = new \Slim\App(["settings" => require CONFIG_DIR.'/settings.php']);

        session_start();

        require CONFIG_DIR.'/container.php';
        require CONFIG_DIR.'/routes.php';

        $app->run();
    }

    public static function loadContainer()
    {
        $app = new \Slim\App(["settings" => require CONFIG_DIR.'/settings.php']);
        require CONFIG_DIR.'/container.php';
        return $container;
    }
}
