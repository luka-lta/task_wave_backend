<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveAuthException;
use TaskWaveBackend\Exception\TaskWaveCategoryNotFoundException;
use TaskWaveBackend\Exception\TaskWaveTaskNotFoundException;
use TaskWaveBackend\Repository\TaskRepository;
use TaskWaveBackend\Value\Categories\Category;
use TaskWaveBackend\Value\Todo\TaskDetails;
use TaskWaveBackend\Value\Todo\TaskStatus;
use TaskWaveBackend\Value\Todo\TaskTimeFrame;
use TaskWaveBackend\Value\Todo\TodoObject;

class TaskService
{
    public function __construct(
        private readonly TaskRepository  $taskRepository,
        private readonly CategoryService $categoryService,
    ) {
    }

    public function createTask(
        int                $ownerId,
        ?int               $categoryId,
        string             $title,
        ?string            $description,
        ?DateTimeImmutable $deadline,
        ?string            $priority,
        ?string            $status,
        ?bool              $pinned
    ): void {
        $categories = $this->categoryService->getCategoriesByOwnerId($ownerId);

        /** @var Category $category */
        foreach ($categories as $category) {
            $categories[] = $category->getCategoryId();
        }

        if ($categoryId && !in_array($categoryId, $categories, true)) {
            throw new TaskWaveCategoryNotFoundException(
                'Category not found',
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        $task = TodoObject::create(
            $ownerId,
            $categoryId,
            TaskDetails::from($title, $description),
            TaskStatus::from($status, $priority),
            $pinned,
            TaskTimeFrame::from($deadline, null, null)
        );

        $this->taskRepository->createTask($task);
    }

    public function editTask(
        int                $ownerId,
        int                $taskId,
        ?int               $categoryId,
        ?string            $title,
        ?string            $description,
        ?DateTimeImmutable $deadline,
        ?string            $priority,
        ?string            $status,
        ?bool              $pinned,
        ?DateTimeImmutable $startedOn,
        ?DateTimeImmutable $finishedOn,
    ): void {
        $task = $this->getTaskById($taskId);

        if ($task->getOwnerId() !== $ownerId) {
            throw new TaskWaveAuthException(
                'Unauthorized access.',
                StatusCodeInterface::STATUS_UNAUTHORIZED
            );
        }

        if ($categoryId) {
            $categories = $this->categoryService->getCategoriesByOwnerId($task->getOwnerId());

            /** @var Category $category */
            foreach ($categories as $category) {
                $categories[] = $category->getCategoryId();
            }

            if (!in_array($categoryId, $categories, true)) {
                throw new TaskWaveCategoryNotFoundException(
                    'Category not found',
                    StatusCodeInterface::STATUS_NOT_FOUND
                );
            }
        }

        $task->setCategoryId($categoryId);
        $task->setTaskDetails(TaskDetails::from($title, $description));
        $task->setTaskStatus(TaskStatus::from($status, $priority));
        $task->setPinned($pinned);
        $task->setTimeFrame(TaskTimeFrame::from($deadline, $startedOn, $finishedOn));

        $this->taskRepository->editTask($task);
    }

    public function deleteTask(int $taskId, int $ownerId): void
    {
        $task = $this->getTaskById($taskId);

        if ($task->getOwnerId() !== $ownerId) {
            throw new TaskWaveAuthException(
                'Unauthorized access.',
                StatusCodeInterface::STATUS_UNAUTHORIZED
            );
        }

        $this->taskRepository->deleteTask($task);
    }

    public function getTaskById(int $taskId): TodoObject
    {
        $task = $this->taskRepository->getTaskById($taskId);

        if ($task === null) {
            throw new TaskWaveTaskNotFoundException(
                'Task not found',
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }

        return $task;
    }

    public function getAllTasksByOwnerId(int $ownerId): array
    {
        $tasks = $this->taskRepository->getAllTasksByOwnerId($ownerId);
        $categories = [];

        /** @var TodoObject $task */
        foreach ($tasks as $task) {
            $categoryId = $task->getCategoryId();

            if ($categoryId !== null) {
                $category = $this->categoryService->findCategoryById($categoryId);

                if (is_object($category)) {
                    $categories[$task->getTodoId()] = $category;
                }
            }
        }

        $formattedTasks = [];
        foreach ($tasks as $task) {
            $formattedTask = $task->toArray();

            $formattedTask['category'] = isset(
                $categories[$task->getTodoId()]
            ) && is_object($categories[$task->getTodoId()])
                ? $categories[$task->getTodoId()]->toArray()
                : null;

            $formattedTasks[] = $formattedTask;
        }

        return $formattedTasks;
    }
}
