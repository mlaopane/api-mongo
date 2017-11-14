<?php

use MykeOn\Controller\Http\{
    GetController,
    PostController,
    PutController,
    DeleteController
};
use MykeOn\Middleware\{
    AccessControlMiddleware,
    CacheMiddleware
};

$app->add(new AccessControlMiddleware());

/** GET request */
$app
    ->get("/{database:[a-z0-9]+}[/{collection:[a-z0-9]+}[/{id:[a-z0-9]+}]]", GetController::class.":handleRequest")
    ->setName('get')
    ->add(new CacheMiddleware($container['cache'], 'request'));

/** ACTION collection request */
$app
    ->post("/{database:[a-z0-9]+}/{collection:[a-z0-9]+}/{action:_[a-z]+}", PostController::class.":handleRequest")
    ->setName('action_collection');

/** ACTION database request */
$app
    ->post("/{database:[a-z0-9]+}/{action:_[a-z]+}", PostController::class.":handleRequest")
    ->setName('action_database');

/** POST request */
$app
    ->post("/{database:[a-z0-9]+}/{collection:[a-z0-9]+}", PostController::class.":handleRequest")
    ->setName('post');

/** PUT request */
$app
    ->put("/{database:[a-z0-9]+}/{collection:[a-z0-9]+}/{id:[a-z0-9]+}", PutController::class.":handleRequest")
    ->setName('put');

/** DELETE request */
$app
    ->delete("/{database:[a-z0-9]+}[/{collection:[a-z0-9]+}[/{id:[a-z0-9]+}]]", DeleteController::class.":handleRequest")
    ->setName('delete_request');
