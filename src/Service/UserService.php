<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Exception\TaskWaveInvalidCredentialsException;
use TaskWaveBackend\Exception\TaskWaveUserNotFoundException;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;
use TaskWaveBackend\Repository\UserRepository;
use TaskWaveBackend\Value\AuthToken\AuthToken;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\Password;
use TaskWaveBackend\Value\User\User;

class UserService
{
    public function __construct(
        private readonly UserRepository       $userRepository,
        private readonly PasswordResetService $passwordResetService,
        private readonly AccessService        $accessService,
    ) {
    }

    public function registerUser(
        string $username,
        string $email,
        string $password,
        ?string $gender = null,
        ?string $profilePicture = null
    ): AuthToken {
        if ($this->userRepository->findByEmail(Email::from($email)) !== null) {
            throw new TaskWaveDatabaseException(
                'Email already exists',
                StatusCodeInterface::STATUS_CONFLICT
            );
        }

        $user = User::fromRaw(null, 4, 'User', $username, $email, $password, $gender, $profilePicture);
        $this->userRepository->save($user);

        return AuthToken::generateToken($user);
    }

    public function loginUser(string $email, string $inputPassword): AuthToken
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

        return AuthToken::generateToken($user);
    }

    public function updateUser(
        int $requesterId,
        int $userId,
        int $roleId,
        string $role,
        string $username,
        string $email,
        string $password,
        ?string $gender = null,
        ?string $profilePicture = null
    ): void {
        if ($this->accessService->accessResource('write', $userId, $requesterId) === false) {
            return;
        }

        if ($this->findUserByEmail($email) !== null) {
            throw new TaskWaveDatabaseException(
                'Email already exists',
                StatusCodeInterface::STATUS_CONFLICT
            );
        }


        $user = User::fromRaw($userId, $roleId, $role, $username, $email, $password, $gender, $profilePicture);

        $this->userRepository->update($user);
    }

    public function deleteUser(int $requesterId, int $userId): void
    {
        if ($this->accessService->accessResource('delete', $userId, $requesterId) === false) {
            return;
        }

        if ($this->findUserById($userId) === null) {
            throw new TaskWaveUserNotFoundException(
                'User not exists with this ID',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        $this->userRepository->delete($userId);
    }

    public function findUserById(int $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new TaskWaveUserNotFoundException(
                'User not exists with this ID',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        return $user;
    }

    public function findUserByEmail(string $email): User
    {
        $user = $this->userRepository->findByEmail(Email::from($email));

        if ($user === null) {
            throw new TaskWaveUserNotFoundException(
                'User not exists with this email',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        return $user;
    }

    public function updatePassword(Email $email, string $newPassword, string $resetToken): void
    {
        $this->passwordResetService->validateResetToken($email, $resetToken);

        $user = $this->findUserByEmail($email->toString());

        $oldPassword = $user->getPassword();

        if ($oldPassword->verify($newPassword)) {
            throw new TaskWaveValidationFailureException(
                'New password cannot be the same as the old password',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        $newPassword = Password::fromPlain($newPassword);

        $user->setPassword($newPassword);
        $this->userRepository->update($user);

        $this->passwordResetService->deletePasswordReset($email);
    }
}
