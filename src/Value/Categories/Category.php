<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Categories;

class Category
{
    private function __construct(
        private readonly ?int $categoryId,
        private readonly int $ownerId,
        private readonly string $name,
        private readonly string $description,
        private readonly ?string $color,
    ) {
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['category_id'],
            $data['owner_id'],
            $data['name'],
            $data['description'],
            $data['color'],
        );
    }

    public static function from(
        ?int $categoryId,
        int $ownerId,
        string $name,
        string $description,
        ?string $color = null,
    ): self {
        return new self(
            $categoryId,
            $ownerId,
            $name,
            $description,
            $color,
        );
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
