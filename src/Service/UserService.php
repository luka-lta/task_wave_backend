<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Exception\TaskWaveInvalidCredentialsException;
use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Repository\UserRepository;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\User;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    public function registerUser(
        string $username,
        string $email,
        string $password,
        ?string $gender = null,
        ?string $profilePicture = null
    ): string {
        if ($this->userRepository->findByEmail(Email::from($email)) !== null) {
            throw new TaskWaveDatabaseException(
                'Email already exists',
                StatusCodeInterface::STATUS_CONFLICT
            );
        }

        $user = User::fromRaw(null, $username, $email, $password, $gender, $profilePicture);
        $this->userRepository->save($user);

        return $this->jwtService->generateJwt($user);
    }

    public function loginUser(string $email, string $inputPassword): string
    {
        $user = $this->userRepository->findByEmail(Email::from($email));

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

        return $this->jwtService->generateJwt($user);
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->update($user);
    }

    public function updateUserFromRaw(
        int $userId,
        string $username,
        string $email,
        string $password,
        ?string $gender = null,
        ?string $profilePicture = null
    ): void {
        $user = User::fromRaw($userId, $username, $email, $password, $gender, $profilePicture);

        $this->userRepository->update($user);
    }

    public function deleteUser(int $userId): void
    {
        $this->userRepository->delete($userId);
    }

    public function findUserById(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail(Email::from($email));
    }
}
