<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Role;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class ChangeUserRoleAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = (int)$request->getAttribute('userId');
        $roleId = (int)$request->getAttribute('roleId');
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        $this->userService->updateRole($roleId, $userId, $decodedToken->getUserId());

        return TaskWaveResult::from(JsonResult::from('Role updated'))->getResponse($response);
    }
}
