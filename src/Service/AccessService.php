<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveAuthException;
use TaskWaveBackend\Repository\RoleRepository;

class AccessService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function accessResource(string $action, int $userId, int $requesterId): bool
    {
        $role = $this->roleRepository->getRole($userId);

        if (!$this->roleRepository->hasPermission($role->getRoleId(), $action)) {
            $this->denyAccess();
        }

        switch ($action) {
            case 'read':
                if ($role->canRead() || $this->isOwner($userId, $requesterId)) {
                    return true;
                }
                break;
            case 'write':
                if ($role->canWrite() || $this->isOwner($userId, $requesterId)) {
                    return true;
                }
                break;
            case 'delete':
                if ($role->canDelete() || $this->isOwner($userId, $requesterId)) {
                    return true;
                }
                break;
            default:
                $this->denyAccess();
        }

        $this->denyAccess();
    }

    private function isOwner(int $userId, int $requesterId): bool
    {
        return $userId === $requesterId;
    }

    private function denyAccess(): void
    {
        throw new TaskWaveAuthException(
            'Unauthorized access.',
            StatusCodeInterface::STATUS_UNAUTHORIZED
        );
    }
}
