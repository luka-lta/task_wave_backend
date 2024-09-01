<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Todo;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class TaskDetails
{
    private function __construct(
        private readonly string  $title,
        private readonly ?string $description,
    ) {
        if (empty($title)) {
            throw new TaskWaveValidationFailureException(
                'Title cannot be empty',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    public static function from(string $title, ?string $description): self
    {
        return new self($title, $description);
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'] ?? null
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
