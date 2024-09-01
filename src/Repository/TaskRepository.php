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
                'title' => $todoObject->getTitle(),
                'description' => $todoObject->getDescription(),
                'deadline' => $todoObject->getDeadline()?->format(DATE_ATOM),
                'priority' => $todoObject->getPriority(),
                'status' => $todoObject->getStatus(),
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
}
