<?php

declare(strict_types=1);

namespace TaskWaveBackend\App\Factory;

use DI\Container;
use DI\ContainerBuilder;
use TaskWaveBackend\ApplicationConfig;

class ContainerFactory
{
    public static function buildContainer(): Container
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(true);
        $container->addDefinitions(new ApplicationConfig());
        return  $container->build();
    }
}
