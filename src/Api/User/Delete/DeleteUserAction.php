<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class DeleteUserAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = (int)$request->getAttribute('userId') ?? null;
        $jwt = $request->getAttribute('jwt');

        if ($jwt['sub'] !== $userId) {
            return TaskWaveResult::from(
                JsonResult::from('Unauthorized access.'),
                StatusCodeInterface::STATUS_UNAUTHORIZED
            )->getResponse($response);
        }

        $validatorError = $this->requestValidator->validate([
            'userId' => $userId,
        ]);

        if ($validatorError) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validatorError]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->userService->deleteUser($userId);

        return TaskWaveResult::from(
            JsonResult::from('User deleted successfully.'),
        )->getResponse($response);
    }
}
