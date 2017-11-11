<?php

use MykeOn\Controller\Http\{
    GetController,
    PostController,
    PutController,
    DeleteController
};
use MykeOn\Middleware\AccessControlMiddleware;

$app->add(new AccessControlMiddleware());

/** GET request */
$app
    ->get("/{database}[/{collection}[/{id}]]", GetController::class.":handleRequest")
    ->setName('get');

/** POST request */
$app
    ->post("/{database}/{collection}", PostController::class.":handleRequest")
    ->setName('post');

/** SEARCH request */
$app
    ->post("/{database}/_search", PostController::class.":handleDatabaseSearchRequest")
    ->setName('database_search');

/** SEARCH request */
$app
    ->post("/{database}/{collection}/_search", PostController::class.":handleCollectionSearchRequest")
    ->setName('collection_search');

/** PUT request */
$app
    ->put("/{database}/{collection}/{id:[0-9]+}", PutController::class.":handleRequest")
    ->setName('put');

/** DELETE request */
$app
    ->delete("/{database}[/{collection}/[/{id}]]", DeleteController::class.":handleRequest")
    ->setName('delete_request');
