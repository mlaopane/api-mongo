<?php
namespace MykeOn\Service\Database\MongoDB;

use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use MykeOn\Service\Database\DatabaseInterface;

class Database implements DatabaseInterface
{
    public function __construct(ContainerInterface $container)
    {
        try {
            $filepath = dirname(dirname(dirname(dirname(__DIR__)))).'/app/config/database.yml';
            $this->database = Yaml::parseFile($filepath)['database'];
        } catch (ParseException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                "message" => "Issue with the database configuration",
                "error" => $e->getMessage(),
            ]);
            die;
        }
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        extract($this->database);
        return "{$type}://{$username}:{$password}@{$ds}:{$port}/{$name}";
    }
}