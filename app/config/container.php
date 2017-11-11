<?php
use MykeOn\Service\Database\MongoDB\DatabaseManager;

$container = $app->getContainer();
$container['db_manager'] = function ($c) {
    return new DatabaseManager();
};
