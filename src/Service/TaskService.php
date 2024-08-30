<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveCategoryNotFoundException;
use TaskWaveBackend\Repository\TaskRepository;
use TaskWaveBackend\Value\Categories\Category;
use TaskWaveBackend\Value\Todo\TodoObject;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly CategoryService $categoryService,
    ) {
    }

    public function createTask(
        int $ownerId,
        ?int $categoryId,
        string $title,
        ?string $description,
        ?DateTimeImmutable $deadline,
        ?string $priority,
        ?string $status,
        ?bool $pinned
    ): void {
        $categories = $this->categoryService->getCategoriesByOwnerId($ownerId);

        /** @var Category $category */
        foreach ($categories as $category) {
            $categories[] = $category->getCategoryId();
        }

        if (!in_array($categoryId, $categories, true)) {
            throw new TaskWaveCategoryNotFoundException('Category not found');
        }

        $task = TodoObject::create($ownerId, $categoryId, $title, $description, $deadline, $priority, $status, $pinned);

        $this->taskRepository->createTask($task);
    }
}
