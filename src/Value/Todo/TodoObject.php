<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Todo;

class TodoObject
{
    private function __construct(
        private readonly ?int       $todoId,
        private readonly int        $ownerId,
        private ?int                $categoryId,
        private TaskDetails         $taskDetails,
        private TaskStatus $taskStatus,
        private ?bool               $pinned,
        private TaskTimeFrame       $timeFrame,
    ) {
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['todo_id'],
            $data['owner_id'],
            $data['category_id'] ?? null,
            TaskDetails::fromDatabase($data),
            TaskStatus::fromDatabase($data),
            (bool)$data['pinned'],
            TaskTimeFrame::fromDatabase($data)
        );
    }

    public static function create(
        int                $ownerId,
        ?int               $categoryId,
        TaskDetails        $taskDetails,
        TaskStatus         $taskStatus,
        ?bool              $pinned,
        TaskTimeFrame      $timeFrame,
    ): self {
        return new self(
            null,
            $ownerId,
            $categoryId,
            $taskDetails,
            $taskStatus,
            $pinned,
            $timeFrame
        );
    }

    public function getTodoId(): ?int
    {
        return $this->todoId;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getTaskDetails(): TaskDetails
    {
        return $this->taskDetails;
    }

    public function getTaskStatus(): TaskStatus
    {
        return $this->taskStatus;
    }

    public function isPinned(): ?bool
    {
        return $this->pinned;
    }

    public function getTimeFrame(): TaskTimeFrame
    {
        return $this->timeFrame;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function setTaskDetails(TaskDetails $taskDetails): void
    {
        $this->taskDetails = $taskDetails;
    }

    public function setTaskStatus(TaskStatus $taskStatus): void
    {
        $this->taskStatus = $taskStatus;
    }

    public function setPinned(?bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    public function setTimeFrame(TaskTimeFrame $timeFrame): void
    {
        $this->timeFrame = $timeFrame;
    }
}
