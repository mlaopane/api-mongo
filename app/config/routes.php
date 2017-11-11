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
    ->get("/{database:[a-z0-9]+}[/{collection:[a-z0-9]+}[/{id}]]", GetController::class.":handleRequest")
    ->setName('get');

/** ACTION collection request */
$app
    ->post("/{database:[a-z0-9]+}/{collection:[a-z0-9]+}/{action:_[a-z]+}", PostController::class.":handleRequest")
    ->setName('collection_action');

/** ACTION database request */
$app
    ->post("/{database:[a-z0-9]+}/{action:_[a-z]+}", PostController::class.":handleRequest")
    ->setName('database_action');

/** POST request */
$app
    ->post("/{database:[a-z0-9]+}/{collection:[a-z0-9]+}", PostController::class.":handleRequest")
    ->setName('post');

/** PUT request */
$app
    ->put("/{database}/{collection}/{id:[0-9]+}", PutController::class.":handleRequest")
    ->setName('put');

/** DELETE request */
$app
    ->delete("/{database}[/{collection}/[/{id}]]", DeleteController::class.":handleRequest")
    ->setName('delete_request');
