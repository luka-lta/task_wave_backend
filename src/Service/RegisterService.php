<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Repository\RegisterRepository;
use TaskWaveBackend\Repository\UserRepository;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\User;

class RegisterService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RegisterRepository $registerRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    public function register(string $email, string $username, string $password): string
    {
        if ($this->userRepository->findByEmail(Email::from($email)) !== null) {
            throw new TaskWaveDatabaseException(
                'Email already exists',
                StatusCodeInterface::STATUS_CONFLICT
            );
        }

        $user = User::fromRegistration($username, $email, $password);
        $this->registerRepository->registerUser($user);

        return $this->jwtService->generateJwt($user);
    }
}
