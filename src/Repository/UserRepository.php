<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\User;

class UserRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findByEmail(Email $email): User|null
    {
        $query = <<<SQL
            SELECT * FROM users WHERE email = :email
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                'email' => $email->toString(),
            ]);

            $result = $statement->fetch();

            if ($result === false) {
                return null;
            }

            return User::fromDatabase($result);
        } catch (TaskWaveDatabaseException $exception) {
            throw new TaskWaveDatabaseException(
                $exception->getMessage(),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function updateLastLoggedIn(User $user): void
    {
        $query = <<<SQL
            UPDATE users SET last_logged_in = NOW() WHERE user_id = :userId
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute([
                'userId' => $user->getUserId(),
            ]);
        } catch (TaskWaveDatabaseException $exception) {
            throw new TaskWaveDatabaseException(
                $exception->getMessage(),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }
}
