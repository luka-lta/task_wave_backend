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

    public function editCategory(Category $category): void
    {
        $query = <<<SQL
            UPDATE 
                categories
            SET 
                name = :name,
                description = :description,
                color = COALESCE(:color, color)
            WHERE 
                category_id = :category_id
        SQL;

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'color' => $category->getColor(),
                'category_id' => $category->getCategoryId()
            ]);
            $this->pdo->commit();
        } catch (PDOException $exception) {
            var_dump($exception->getMessage());
            $this->pdo->rollBack();
            throw new TaskWaveDatabaseException(
                'Failed to edit categorie',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function findCategoryByName(string $name): ?Category
    {
        $query = <<<SQL
            SELECT 
                *
            FROM 
                categories
            WHERE 
                name = :name
        SQL;

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['name' => $name]);
            $result = $stmt->fetch();

            if ($result === false) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to find categorie',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return Category::fromDatabase($result);
    }
}
