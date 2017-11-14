<?php
namespace MykeOn;

use MykeOn\Service\Database\MongoDB\DatabaseManager;
use MykeOn\Service\Cache\Cache;

$container = $app->getContainer();
$container['db_manager'] = new DatabaseManager();
$container['cache'] = new Cache();
