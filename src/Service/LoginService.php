<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveInvalidCredentialsException;
use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Value\AuthToken\AuthToken;

class LoginService
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function loginUser(string $email, string $inputPassword): AuthToken
    {
        $user = $this->userService->findUserByEmail($email);

        if ($user->isDisabled()) {
            throw new TaskWaveInvalidCredentialsException(
                'User is disabled',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if ($user->isBanned()) {
            throw new TaskWaveInvalidCredentialsException(
                'User is banned',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if ($user === null) {
            throw new TaskWaveUserNotFoundException(
                'User not exists with this email',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if (!$user->getPassword()->verify($inputPassword)) {
            throw new TaskWaveInvalidCredentialsException(
                'Invalid password',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        return AuthToken::generateToken($user);
    }
}
