<?php

use MykeOn\Controller\Http\{
    GetController,
    PostController,
    SearchController,
    ReplaceController,
    PatchController,
    DeleteController
};
use MykeOn\Middleware\{
    HeadersMiddleware,
    CacheRequestMiddleware
};

/* -------------------- */

$app
->add(new CacheRequestMiddleware($container['cache']))
->add(new HeadersMiddleware())
;

/** Requests uri MUST provide the database **/
$app->group("/{database:[a-z0-9]+}", function () {

    /** READ **/
    $this->get("[/{collection:[a-z0-9]+}[/{id:[a-z0-9]+}]]", GetController::class.":handleRequest")->setName('get');

    /** CREATE **/
    $this->post("/{collection:[a-z0-9]+}", PostController::class.":handleRequest")->setName('search_database');

    /** SEARCH database **/
    $this->post("/_search", SearchController::class.":handleRequest")->setName('search_database');

    /** SEARCH collection **/
    $this->post("/{collection:[a-z0-9]+}/_search", SearchController::class.":handleRequest")->setName('search_collection');

    /** REPLACE **/
    $this->put("/{collection:[a-z0-9]+}/{id:[a-z0-9]+}", ReplaceController::class.":handleRequest")->setName('put');

    /** UPDATE collection **/
    $this->patch("/{collection:[a-z0-9]+}/_{action:[a-z0-9]+}", PatchController::class.":handleRequest")->setName('patch');

    /** UPDATE document **/
    $this->patch("/{collection:[a-z0-9]+}/{id:[a-z0-9]+}/_{action:[a-z0-9]+}", PatchController::class.":handleRequest")->setName('patch');

    /** DELETE request */
    $this->delete("[/{collection:[a-z0-9]+}[/{id:[a-z0-9]+}]]", DeleteController::class.":handleRequest")->setName('delete');

});
