<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\User\User;

class RegisterRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function registerUser(User $user): void
    {
        $query = <<<SQL
            INSERT INTO users (username, email, password)
            VALUES (:username, :email, :password)
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                'username' => $user->getUsername()->toString(),
                'email' => $user->getEmail()->toString(),
                'password' => $user->getPassword()->toString(),
            ]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to register user.',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }
}
