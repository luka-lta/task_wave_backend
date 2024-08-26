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
            $stmt = $this->pdo->prepare('INSERT INTO password_resets (email, token, created_at, expired_at) VALUES (:email, :token, :created_at, :expired_at)');
            $stmt->execute([
                'email' => $email->toString(),
                'token' => $token,
                'created_at' => date('Y-m-d H:i:s'),
                'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);
        } catch (PDOException $e) {

            var_dump($token);

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
}
