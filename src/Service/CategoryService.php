<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Repository\CategoryRepository;
use TaskWaveBackend\Value\Categories\Category;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categorieRepository,
    ) {
    }

    public function createCategorie(int $ownerId, string $name, string $descrption, string $color = null): void
    {
        // TODO: Check if category already exists
        $category = Category::from(null, $ownerId, $name, $descrption, $color);

        $this->categorieRepository->createCategorie($category);
    }
}
