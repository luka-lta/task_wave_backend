<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Pagination;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Page
{
    private function __construct(
        private readonly int $pageNumber,
    ) {
        if ($pageNumber < 1) {
            throw new TaskWaveValidationFailureException(
                'Page number must be greater than 0',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }

    public static function from(int $pageNumber): self
    {
        return new self($pageNumber);
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }
}
