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

        if ($category && $category->getOwnerId() !== $ownerId) {
            throw new TaskWaveAuthException(
                'Unauthorized access.',
                StatusCodeInterface::STATUS_UNAUTHORIZED
            );
        }

        if ($category && $category->getName() === $name) {
            throw new TaskWaveUserNotFoundException(
                'Category already exists',
                StatusCodeInterface::STATUS_CONFLICT
            );
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

        if ($category->getOwnerId() !== $ownerId) {
            throw new TaskWaveUserNotFoundException(
                'Unauthorized access.',
                StatusCodeInterface::STATUS_UNAUTHORIZED
            );
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
