<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Edit\Ban;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class UnBanUserAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = (int)$request->getAttribute('userId');
        $requesterId = DecodedToken::fromArray($request->getAttribute('jwt'))->getUserId();

        $this->userService->unBanUser($requesterId, $userId);

        return TaskWaveResult::from(JsonResult::from('User has been unbaned.'))->getResponse($response);
    }
}
