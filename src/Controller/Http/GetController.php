<?php
namespace MykeOn\Controller\Http;

use Slim\Http\Request;
use Slim\Http\Response;
use MongoDB\BSON\ObjectId;

class GetController extends HttpController
{
    /**
     * @inheritdoc
     */
    public function handleDatabaseRequest(Request $request, Response $response, array $arguments): Response
    {
        if ($this->cache->has($this->cacheKey)) {
            $fromCache = $this->cache->get($this->cacheKey);
            return $response->withJson($fromCache, 200);
        }

        if (empty($collections = $this->get('db_manager')->fetchDatabase($this->database))) {
            return $response
                ->withStatus(204, 'No data found');
        }

        return $response->withJson(['collections' => $collections], 200);
    }

    /**
     * @inheritdoc
     */
    public function handleCollectionRequest(Request $request, Response $response, array $arguments): Response
    {
        // Get a document by its id
        if (!empty($arguments["id"])) {
            $content['data'] = $this->collection->findOne(['_id' => new ObjectId($arguments["id"])]);
        // Get one or many documents
        } else {
            $content['data'] = $this->collection->find()->toArray();
        }

        if (empty($content['data'])) {
            return $response->withStatus(200, 'No data found');
        }
        return $response->withJson($content);
    }
}
