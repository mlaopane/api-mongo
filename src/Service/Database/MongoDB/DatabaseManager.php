<?php
namespace MykeOn\Service\Database\MongoDB;

use MongoDB\Database;
use MongoDB\Collection;
use Psr\Container\ContainerInterface;

class DatabaseManager
{
    /**
     * Fetch all the documents from the provided database
     *
     * @param  Database $database
     *
     * @return array
     */
    public function fetchDatabase(Database $database, array $filter = []): array
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

    /**
     * [insertCollections description]
     * @param  Database $database    [description]
     * @param  array    $collections [description]
     * @return array                 [description]
     */
    public function insertMany(string $collectionName, array $documents): array
    {
        $result = $database->selectCollection($collectionName)->insertMany($documents);

        $responseBody['count'] = $result->getInsertedCount();
        $responseBody['ids'] = $result->getInsertedIds();

        return $responseBody;
    }

    /**
     *
     * @param  Database $database    [description]
     * @param  array    $collections [description]
     * @return array                 [description]
     */
    public function upsertCollections(Database $database, array $collections): array
    {
        $responseBody['collections'] = ['count' => 0];

        foreach ($collections as $collection) {
            $collectionCount = 0;
            $insertedIds = [];
            foreach ($collection['data'] as $document) {
                $result = $database->selectCollection($collection['name'])->updateOne(
                    $document,
                    ['$set' => $document],
                    ['upsert' => $collection['upsert']]
                );
                if ($result->getUpsertedCount()) {
                    $collectionCount += 1;
                    $insertedIds[] = $result->getUpsertedId();
                }
            }
            $responseBody['collections']['count'] += $collectionCount;
            $responseBody['collections']['data'][] = [
                'name'  => $collection['name'],
                'count' => $collectionCount,
                'ids'   => $insertedIds,
            ];
        }

        return $responseBody;
    }
}
