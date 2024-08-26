<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveInvalidTokenException;
use TaskWaveBackend\Repository\PasswordResetRepository;
use TaskWaveBackend\Value\PasswordReset;
use TaskWaveBackend\Value\User\Email;

class PasswordResetService
{
    public function __construct(private readonly PasswordResetRepository $repository)
    {
    }

    public function createPasswordResetToken(Email $email): void
    {
        $token = bin2hex(random_bytes(16));

        $resetLink = 'https://localhost:3000/new-password?token=' . $token;

        mail(
            $email->toString(),
            'Password Reset',
            'Klicken Sie auf den folgenden Link, um Ihr Passwort zurückzusetzen:' . $resetLink
        );

        $this->repository->createPasswordResetToken($email, $token);
    }


    public function validateResetToken(Email $email, string $token): ?PasswordReset
    {
        $passwordReset  = $this->repository->getPasswordReset($email);

        if ($token !== $passwordReset->getToken()) {
            throw new TaskWaveInvalidTokenException(
                'Invalid reset token',
                StatusCodeInterface::STATUS_UNAUTHORIZED,
            );
        }

        if ($passwordReset->getExpiredAt()->getTimestamp() < time()) {
            throw new TaskWaveInvalidTokenException(
                'Reset token expired',
                StatusCodeInterface::STATUS_UNAUTHORIZED,
            );
        }

        return $passwordReset;
    }
}
