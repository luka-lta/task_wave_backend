<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Todo;

use DateTimeImmutable;

class TodoObject
{
    private function __construct(
        private readonly ?int                $todoId,
        private readonly int                $ownerId,
        private readonly ?int               $categoryId,
        private readonly string             $title,
        private readonly ?string             $description,
        private readonly ?DateTimeImmutable  $deadline,
        private readonly ?string             $priority,
        private readonly ?string             $status,
        private readonly ?bool               $pinned,
        private readonly ?DateTimeImmutable $startedOn,
        private readonly ?DateTimeImmutable $finishedOn,
    ) {
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['todo_id'],
            $data['owner_id'],
            $data['category_id'] ?? null,
            $data['title'],
            $data['description'] ?? null,
            new DateTimeImmutable($data['deadline']) ?? null,
            $data['priority'] ?? null,
            $data['status'] ?? null,
            (bool)$data['pinned'],
            $data['started_on'] ? new DateTimeImmutable($data['started_on']) : null,
            $data['finished_on'] ? new DateTimeImmutable($data['finished_on']) : null,
        );
    }

    public static function create(
        int                $ownerId,
        ?int               $categoryId,
        string             $title,
        ?string             $description,
        ?DateTimeImmutable  $deadline,
        ?string             $priority,
        ?string             $status,
        ?bool               $pinned,
    ): self {
        return new self(
            null,
            $ownerId,
            $categoryId,
            $title,
            $description,
            $deadline,
            $priority,
            $status,
            $pinned,
            null,
            null,
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDeadline(): ?DateTimeImmutable
    {
        return $this->deadline;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function isPinned(): ?bool
    {
        return $this->pinned;
    }

    public function getStartedOn(): ?DateTimeImmutable
    {
        return $this->startedOn;
    }

    public function getFinishedOn(): ?DateTimeImmutable
    {
        return $this->finishedOn;
    }
}
