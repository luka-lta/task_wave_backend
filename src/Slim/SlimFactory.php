<?php

declare(strict_types=1);

namespace TaskWaveBackend\Slim;

use DI\Bridge\Slim\Bridge;
use Psr\Container\ContainerInterface;
use Slim\App;

class SlimFactory
{
    public static function create(ContainerInterface $container): App
    {
        $app = Bridge::create($container);
        return $app;
    }
}
