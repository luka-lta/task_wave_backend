<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\Pagination\Pagination;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\User;

class UserRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(int $userId): ?User
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT users.*, roles.id AS role_id, roles.name AS role_name
                FROM users
                JOIN roles ON users.role_id = roles.id
                WHERE users.user_id = :user_id');
            $stmt->execute(['user_id' => $userId]);
            $userData = $stmt->fetch();
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to fetch user by ID',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $userData ? User::fromDatabase($userData) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT users.*, roles.id AS role_id, roles.name AS role_name
                FROM users
                JOIN roles ON users.role_id = roles.id
                WHERE users.email = :email');
            $stmt->execute(['email' => $email->toString()]);
            $userData = $stmt->fetch();
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to fetch user by email',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $userData ? User::fromDatabase($userData) : null;
    }

    public function save(User $user): void
    {
        try {
            $stmt = $this->pdo->prepare('
            INSERT INTO users (username, email, password, gender, profile_picture_path) 
            VALUES (:username, :email, :password, :gender, :profile_picture_path)
        ');

            $stmt->execute([
                'username' => $user->getUsername()->toString(),
                'email' => $user->getEmail()->toString(),
                'password' => $user->getPassword()->toString(),
                'gender' => $user->getGender()?->toString(),
                'profile_picture_path' => $user->getProfilePicture()?->toString(),
            ]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to save user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function update(User $user): void
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('
            UPDATE users SET 
                username = :username, 
                email = :email, 
                password = :password, 
                gender = :gender, 
                profile_picture_path = :profile_picture_path 
            WHERE user_id = :user_id
        ');

            $stmt->execute([
                'user_id' => $user->getUserId(),
                'username' => $user->getUsername()->toString(),
                'email' => $user->getEmail()->toString(),
                'password' => $user->getPassword()->toString(),
                'gender' => $user->getGender()?->toString(),
                'profile_picture_path' => $user->getProfilePicture()?->toString(),
            ]);
            $this->pdo->commit();
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            throw new TaskWaveDatabaseException(
                'Failed to update user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function delete(int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE users SET disabled = 1 WHERE user_id = :user_id');
            $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to delete user',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function updateRole(int $roleId, int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare('UPDATE users SET role_id = :role_id WHERE user_id = :user_id');
            $stmt->execute(['role_id' => $roleId, 'user_id' => $userId]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to update user role',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    public function getAll(Pagination $pagination): ?array
    {
        try {
            $stmt = $this->pdo->prepare('
                SELECT users.*, roles.id AS role_id, roles.name AS role_name
                FROM users
                JOIN roles ON users.role_id = roles.id
                LIMIT :limit OFFSET :offset
            ');
            $stmt->execute([
                'limit' => $pagination->getPageSize()->getPageSize(),
                'offset' => $pagination->getOffset(),
            ]);
            $usersData = $stmt->fetchAll();

            if (empty($usersData)) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to fetch all users',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $usersData;
    }
}
