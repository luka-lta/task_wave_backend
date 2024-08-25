<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\Categories\Category;

class CategoryRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function createCategorie(Category $category): void
    {
        $query = <<<SQL
            INSERT INTO 
                categories (owner_id, name, description, color) 
            VALUES 
                (:owner_id, :name, :description, :color)
        SQL;

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'owner_id' => $category->getOwnerId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'color' => $category->getColor()
            ]);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
            throw new TaskWaveDatabaseException(
                'Failed to create categorie',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }
}
