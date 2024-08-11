<?php

declare(strict_types=1);

namespace TaskWaveBackend;

use DI\Definition\Source\DefinitionArray;
use Monolog\Logger;
use PDO;
use TaskWaveBackend\App\Factory\LoggerFactory;
use TaskWaveBackend\App\Factory\PdoFactory;

use function DI\factory;

class ApplicationConfig extends DefinitionArray
{
    public function __construct()
    {
        parent::__construct($this->getConfig());
    }

    private function getConfig(): array
    {
        return [
            Logger::class => factory(new LoggerFactory()),
            PDO::class => factory(new PdoFactory()),
        ];
    }
}
