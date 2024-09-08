<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Todo;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class TaskStatus
{
    private const STATUS_TODO = 'ToDo';
    private const STATUS_IN_PROGRESS = 'In progress';
    private const STATUS_FINISHED = 'Finished';

    private const PRIORITY_NO = 'NO-PRIORITY';
    private const PRIORITY_LOW = 'LOW';
    private const PRIORITY_MEDIUM = 'MEDIUM';
    private const PRIORITY_HIGH = 'HIGH';

    private const ALLOWED_STATUSES = [
        self::STATUS_TODO,
        self::STATUS_IN_PROGRESS,
        self::STATUS_FINISHED,
    ];

    private const ALLOWED_PRIORITIES = [
        self::PRIORITY_NO,
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    private function __construct(
        private readonly ?string $status = self::STATUS_TODO,
        private readonly ?string $priority = self::PRIORITY_NO,
    ) {
        if ($status && !in_array($status, self::ALLOWED_STATUSES, true)) {
            throw new TaskWaveValidationFailureException(
                'Invalid status',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if ($priority && !in_array($priority, self::ALLOWED_PRIORITIES, true)) {
            throw new TaskWaveValidationFailureException(
                'Invalid priority',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    public static function from(
        ?string $status = self::STATUS_TODO,
        ?string $priority = self::PRIORITY_NO,
    ): self {
        return new self($status, $priority);
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['status'] ?? self::STATUS_TODO,
            $data['priority'] ?? self::PRIORITY_NO,
        );
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }
}
