<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveInvalidCredentialsException;
use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Repository\UserRepository;
use TaskWaveBackend\Value\User\Email;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    public function loginUser(Email $email, string $inputPassword): string
    {
        $user = $this->userRepository->findByEmail($email);

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

        $this->userRepository->updateLastLoggedIn($user);
        return $this->jwtService->generateJwt($user);
    }
}
