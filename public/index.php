<?php

use TaskWaveBackend\App\Factory\ContainerFactory;
use TaskWaveBackend\Slim\SlimFactory;

require __DIR__ . '/../vendor/autoload.php';

try {
    $container = ContainerFactory::buildContainer();
    $app = SlimFactory::create($container);
    $app->run();
} catch (Throwable $throwable) {
    echo $throwable->getMessage();
}
