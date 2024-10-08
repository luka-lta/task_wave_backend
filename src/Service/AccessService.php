<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveAccessDeniedException;
use TaskWaveBackend\Exception\TaskWaveAuthException;
use TaskWaveBackend\Repository\RoleRepository;

class AccessService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function accessUserResource(string $action, int $userId, int $resourceId): bool
    {
        $role = $this->roleRepository->getRole($userId);

        if (!$this->roleRepository->hasPermission($role->getRoleId(), $action)) {
            $this->denyAccess();
        }

        switch ($action) {
            case 'read':
                if ($role->canRead() || $this->isOwner($userId, $resourceId)) {
                    return true;
                }
                break;
            case 'write':
                if ($role->canWrite() || $this->isOwner($userId, $resourceId)) {
                    return true;
                }
                break;
            case 'delete':
                if ($role->canDelete() || $this->isOwner($userId, $resourceId)) {
                    return true;
                }
                break;
            default:
                $this->denyAccess();
        }

        $this->denyAccess();
    }

    public function accessResource(string $action, int $userId): bool
    {
        $role = $this->roleRepository->getRole($userId);

        if (!$this->roleRepository->hasPermission($role->getRoleId(), $action)) {
            $this->denyAccess();
        }

        switch ($action) {
            case 'read':
                if ($role->canRead()) {
                    return true;
                }
                break;
            case 'write':
                if ($role->canWrite()) {
                    return true;
                }
                break;
            case 'delete':
                if ($role->canDelete()) {
                    return true;
                }
                break;
            default:
                $this->denyAccess();
        }

        $this->denyAccess();
    }

    public function canChangeRole(int $userId, int $requestedRole): bool
    {
        $role = $this->roleRepository->getRole($userId);

        return $role->getRoleId() <= $requestedRole;
    }

    private function isOwner(int $userId, int $requesterId): bool
    {
        return $userId === $requesterId;
    }

    private function denyAccess(): void
    {
        throw new TaskWaveAccessDeniedException(
            'Unauthorized access.',
            StatusCodeInterface::STATUS_UNAUTHORIZED
        );
    }
}
