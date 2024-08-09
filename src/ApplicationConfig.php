<?php

declare(strict_types=1);

namespace TaskWaveBackend;

use DI\Definition\Source\DefinitionArray;

class ApplicationConfig extends DefinitionArray
{
    public function __construct()
    {
        parent::__construct($this->getConfig());
    }

    private function getConfig(): array
    {
        return [

        ];
    }
}
