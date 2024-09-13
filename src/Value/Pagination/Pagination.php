<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Pagination;

class Pagination
{
    private function __construct(
        private readonly Page $page,
        private readonly PageSize $pageSize,
        private ?int $totalRecords = 0,
        private ?array $data = []
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

    public function setTotalRecords(?int $totalRecords): void
    {
        $this->totalRecords = $totalRecords;
    }

    public function getTotalRecords(): ?int
    {
        return $this->totalRecords;
    }

    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getMaxPages(): int
    {
        return (int) ceil($this->totalRecords / $this->pageSize->getPageSize());
    }

    public function getMinPages(): int
    {
        return 1;
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
