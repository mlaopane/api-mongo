<?php
namespace MykeOn;

use MykeOn\Service\Database\MongoDB\Database;
use MykeOn\Service\Database\MongoDB\DatabaseManager;
use MykeOn\Service\Cache\Cache;

$container = $app->getContainer();
$container['db'] = function ($container) {
    return new Database($container);
};
$container['db_manager'] = function ($container) {
    return new DatabaseManager();
};
$container['cache'] = function ($container) {
    return new Cache();
};
$container['string_object'] = function ($container) {
    return new StringObject();
};
