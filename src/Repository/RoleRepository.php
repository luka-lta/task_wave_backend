<?php

declare(strict_types=1);

namespace TaskWaveBackend\Repository;

use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;
use TaskWaveBackend\Exception\TaskWaveDatabaseException;
use TaskWaveBackend\Value\User\Role;

class RoleRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function getAvailableRoles(): ?array
    {
        $sql = <<<SQL
                SELECT roles.name, roles.id FROM roles
        SQL;

        try {
            $stmt = $this->pdo->query($sql);
            $roles = $stmt->fetchAll();

            if ($roles === false) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to get available roles',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $roles;
    }

    public function getRole(int $userId): ?Role
    {
        $sql = <<<SQL
                SELECT roles.name, roles.id FROM users
                JOIN roles ON users.role_id = roles.id
                WHERE users.user_id = :id
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId]);
            $role = $stmt->fetch();

            if ($role === false) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to get role',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return Role::from($role['id'], $role['name']);
    }

    public function findById(int $roleId): ?Role
    {
        $sql = <<<SQL
                SELECT roles.name, roles.id FROM roles
                WHERE roles.id = :id
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $roleId]);
            $role = $stmt->fetch();

            if ($role === false) {
                return null;
            }
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to find role',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return Role::from($role['id'], $role['name']);
    }

    public function getPermissions(int $roleId): array
    {
        $sql = <<<SQL
                SELECT permissions.name FROM role_permissions
                JOIN permissions ON role_permissions.permission_id = permissions.id
                WHERE role_permissions.role_id = :role_id
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['role_id' => $roleId]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to get permissions',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function hasPermission(int $roleId, string $permission): bool
    {
        $sql = <<<SQL
                SELECT COUNT(*) FROM role_permissions
                JOIN permissions ON role_permissions.permission_id = permissions.id
                WHERE role_permissions.role_id = :role_id
                AND permissions.name = :permission
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['role_id' => $roleId, 'permission' => $permission]);
        } catch (PDOException $exception) {
            throw new TaskWaveDatabaseException(
                'Failed to check permission',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                $exception
            );
        }

        return $stmt->fetchColumn() > 0;
    }
}
