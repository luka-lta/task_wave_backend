<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Pagination;

class Pagination
{
    private function __construct(
        private readonly Page $page,
        private readonly PageSize $pageSize
    ) {
    }

    public static function from(Page $page, PageSize $pageSize): self
    {
        return new self($page, $pageSize);
    }

    public function getOffset(): int
    {
        return ($this->page->getPageNumber() - 1) * $this->pageSize->getPageSize();
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getPageSize(): PageSize
    {
        return $this->pageSize;
    }
}
