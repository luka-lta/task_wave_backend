<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveRoleNotFoundException;
use TaskWaveBackend\Repository\RoleRepository;
use TaskWaveBackend\Value\User\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly AccessService $accessService,
    ) {
    }

    public function getAvailableRoles(int $requesterId): ?array
    {
        $this->accessService->accessResource('read', $requesterId);

        return $this->roleRepository->getAvailableRoles();
    }

    public function findById(int $roleId): Role
    {
        $role = $this->roleRepository->findById($roleId);

        if ($role === null) {
            throw new TaskWaveRoleNotFoundException(
                'Role not exists with this id',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        return $role;
    }
}
