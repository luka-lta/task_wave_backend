<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\Todo\TodoObject;

class TaskRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function createTask(TodoObject $todoObject): void
    {
        $sql = <<<SQL
            INSERT INTO
                todos (owner_id, category_id, title, description, deadline, priority, status, pinned)
            VALUES (
                    :ownerId, 
                    :categoryId,
                    :title,
                    :description,
                    :deadline,
                    COALESCE(:priority, priority),
                    COALESCE(:status, status),
                    COALESCE(:pinned, pinned)
                    )
        SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                'ownerId' => $todoObject->getOwnerId(),
                'categoryId' => $todoObject->getCategoryId(),
                'title' => $todoObject->getTaskDetails()->getTitle(),
                'description' => $todoObject->getTaskDetails()->getDescription(),
                'deadline' => $todoObject->getTimeFrame()->getDeadline()?->format(DATE_ATOM),
                'priority' => $todoObject->getTaskStatus()->getPriority(),
                'status' => $todoObject->getTaskStatus()->getStatus(),
                'pinned' => (int) $todoObject->isPinned(),
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
            throw new TaskWaveDatabaseException(
                'Failed to create task',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function editTask(TodoObject $todoObject): void
    {
        $sql = <<<SQL
            UPDATE
                todos
            SET
                category_id = :categoryId,
                title = :title,
                description = :description,
                deadline = :deadline,
                priority = COALESCE(:priority, priority),
                status = COALESCE(:status, status),
                pinned = COALESCE(:pinned, pinned),
                started_on = :startedOn,
                finished_on = :finishedOn
            WHERE
                todo_id = :taskId
        SQL;
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                'categoryId' => $todoObject->getCategoryId(),
                'title' => $todoObject->getTaskDetails()->getTitle(),
                'description' => $todoObject->getTaskDetails()->getDescription(),
                'deadline' => $todoObject->getTimeFrame()->getDeadline()?->format(DATE_ATOM),
                'priority' => $todoObject->getTaskStatus()->getPriority(),
                'status' => $todoObject->getTaskStatus()->getStatus(),
                'pinned' => (int) $todoObject->isPinned(),
                'startedOn' => $todoObject->getTimeFrame()->getStartedOn()?->format(DATE_ATOM),
                'finishedOn' => $todoObject->getTimeFrame()->getFinishedOn()?->format(DATE_ATOM),
                'taskId' => $todoObject->getTodoId(),
            ]);
            $this->pdo->commit();
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            throw new TaskWaveDatabaseException(
                'Failed to update task',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function getTaskById(int $taskId): ?TodoObject
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                todos
            WHERE
                todo_id = :taskId
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute(['taskId' => $taskId]);
            $task = $statement->fetch();

            if (!$task) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to fetch task',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return TodoObject::fromDatabase($task);
    }

    public function getAllTasksByOwnerId(int $ownerId): ?array
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                todos
            WHERE
                owner_id = :ownerId
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute(['ownerId' => $ownerId]);
            $tasks = $statement->fetchAll();

            if (!$tasks) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to fetch tasks',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return array_map(fn($task) => TodoObject::fromDatabase($task), $tasks);
    }
}
