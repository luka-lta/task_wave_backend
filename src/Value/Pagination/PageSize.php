<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Pagination;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class PageSize
{
    private function __construct(
        private readonly int $pageSize
    ) {
        if ($pageSize < 1) {
            throw new TaskWaveValidationFailureException(
                'Page size must be greater than 0',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }

    public static function from(int $pageSize): self
    {
        return new self($pageSize);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
