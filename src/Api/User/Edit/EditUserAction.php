<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class EditUserAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userId = (int)$request->getAttribute('userId') ?? null;
        $body = $request->getParsedBody();
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        $email =  $body['email'] ?? null;
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;
        $gender = $body['gender'] ?? null;
        $profilePicture = $body['profilePicture'] ?? null;

        $validatorError = $this->requestValidator->validate([
            'userId' => $userId,
            'email' => $email,
            'username' => $username,
            'password' => $password,
        ]);

        if ($validatorError) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validatorError]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->userService->updateUser(
            $decodedToken->getUserId(),
            $userId,
            $username,
            $email,
            $password,
            $gender,
            $profilePicture
        );

        return TaskWaveResult::from(
            JsonResult::from('User updated!'),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
