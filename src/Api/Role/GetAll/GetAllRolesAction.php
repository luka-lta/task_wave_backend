<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\RoleService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class GetAllRolesAction extends TaskWaveAction
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        $roles = $this->roleService->getAvailableRoles($decodedToken->getUserId());

        if (!$roles) {
            return TaskWaveResult::from(
                JsonResult::from('No roles found'),
                StatusCodeInterface::STATUS_NOT_FOUND
            )->getResponse($response);
        }

        return TaskWaveResult::from(JsonResult::from('Roles found', [
            'roles' => $roles
        ]))->getResponse($response);
    }
}
