<?php
namespace MykeOn\Service\Database\MongoDB;

use MongoDB\Database;
use MongoDB\Collection;
use Psr\Container\ContainerInterface;

class DatabaseManager
{
    public function fetchData($base, array $filter = [])
    {
        if ($base instanceof Database) {
            return $this->fetchDatabaseData($base, $filter);
        }
        if ($base instanceof Collection) {
            return $this->fetchCollectionData($base, $filter);
        }
        $baseType = gettype($base);
        $given = $baseType === 'object' ? get_class($base) : $baseType;
        $expected = Database::class." or ".Collection::class;
        $message = "Expected base argument to be an instance of \"".$expected."\". ".$given." given !";
        throw new \InvalidArgumentException($message);
    }

    /**
     * Fetch all the documents from the provided database
     * @param  Database $database
     * @return array
     */
    public function fetchDatabaseData(Database $database, array $filter = []): array
    {
        $collectionIterator = $database->listCollections();
        $collectionIterator->rewind();
        $collections = [];

        // Grab all documents from the database's collections
        while ($collectionIterator->valid()) {
            $collectionName = $collectionIterator->current()->getName();
            // Add a collection to the response body
            if (!empty($data = $database->$collectionName->find($filter)->toArray())) {
                $collections[] = [
                    'name' => $collectionName,
                    'data' => $data,
                ];
            }
            $collectionIterator->next();
        }

        return $collections;
    }
}
