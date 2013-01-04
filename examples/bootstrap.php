<?php

include __DIR__ . '/../tests/bootstrap.php';

$logger = null;
if (class_exists('Monolog\Logger')) {
    $logger = new Monolog\Logger('PhpCache');
}

PhpCache\CacheManager::setup(new PhpCache\Backends\Memory(), $logger);
