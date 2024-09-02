<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Categories;

class Category
{
    private function __construct(
        private readonly ?int $categoryId,
        private readonly int $ownerId,
        private string $name,
        private string $description,
        private ?string $color,
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

    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'ownerId' => $this->ownerId,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
        ];
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
