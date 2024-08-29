<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\PasswordReset;
use TaskWaveBackend\Value\User\Email;

class PasswordResetRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function createPasswordResetToken(Email $email, string $token): void
    {
        try {
            $query = <<<SQL
                INSERT INTO
                    password_resets (email, token, created_at, expired_at)
                VALUES
                    (:email, :token, :created_at, :expired_at) 
                ON DUPLICATE KEY UPDATE 
                    token = :token_update, 
                    created_at = :created_at_update, 
                    expired_at = :expired_at_update
            SQL;

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'email' => $email->toString(),
                'token' => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                'token_update' => $token,
                'created_at_update' => date('Y-m-d H:i:s'),
                'expired_at_update' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            throw new TaskWaveDatabaseException(
                'Failed to create password reset token',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function getPasswordReset(Email $email): ?PasswordReset
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM password_resets WHERE email = :email');
            $stmt->execute(['email' => $email->toString()]);
            $data = $stmt->fetch();

            if ($data === false) {
                return null;
            }
            return PasswordReset::fromDatabase($data);
        } catch (PDOException $e) {
            throw new TaskWaveDatabaseException(
                'Failed to get password reset informations',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function deletePasswordReset(Email $email): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM password_resets WHERE email = :email');
            $stmt->execute(['email' => $email->toString()]);
        } catch (PDOException $e) {
            throw new TaskWaveDatabaseException(
                'Failed to delete password reset informations',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
