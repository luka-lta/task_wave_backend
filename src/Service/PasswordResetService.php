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

    public function createPasswordResetToken(Email $email, string $token): void
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


    public function getPasswordReset(Email $email): ?PasswordReset
    {
        $passwordReset  = $this->repository->getPasswordReset($email);

        if ($passwordReset === null) {
            throw new TaskWaveInvalidTokenException(
                'No reset token found',
                StatusCodeInterface::STATUS_NOT_FOUND,
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
