<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveAuthException;
use TaskWaveBackend\Exception\TaskWaveCategoryNotFoundException;
use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Repository\CategoryRepository;
use TaskWaveBackend\Value\Categories\Category;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categorieRepository,
        private readonly AccessService $accessService,
    ) {
    }

    public function createCategorie(int $ownerId, string $name, string $descrption, string $color = null): void
    {
        if ($this->findCategoryByName($name)) {
            throw new TaskWaveUserNotFoundException('Category already exists');
        }

        $category = Category::from(null, $ownerId, $name, $descrption, $color);

        $this->categorieRepository->createCategorie($category);
    }

    public function editCategory(
        int $categoryId,
        int $ownerId,
        string $name,
        string $description,
        string $color = null
    ): void {
        $category = $this->findCategoryByName($name);

        if ($this->accessService->accessResource('edit', $ownerId, $category->getOwnerId()) === false) {
            return;
        }

        $category = Category::from($categoryId, $ownerId, $name, $description, $color);

        $this->categorieRepository->editCategory($category);
    }

    public function deleteCategory(int $ownerId, int $categoryId): void
    {
        $category = $this->findCategoryById($categoryId);

        if ($category === null) {
            throw new TaskWaveUserNotFoundException('Category not found');
        }

        if ($this->accessService->accessResource('delete', $ownerId, $category->getOwnerId()) === false) {
            return;
        }

        $this->categorieRepository->deleteCategory($categoryId);
    }

    public function findCategoryById(int $categoryId): ?Category
    {
        return $this->categorieRepository->findCategoryById($categoryId);
    }

    public function findCategoryByName(string $name): ?Category
    {
        return $this->categorieRepository->findCategoryByName($name);
    }

    public function getCategoriesByOwnerId(int $ownerId): array
    {
        $categories = $this->categorieRepository->getCategoriesByOwnerId($ownerId);

        if ($categories === null) {
            throw new TaskWaveCategoryNotFoundException(
                'No categories found',
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return $categories;
    }
}
