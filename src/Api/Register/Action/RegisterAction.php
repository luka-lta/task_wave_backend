<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Register\Action;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class RegisterAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService      $userService,
        private readonly RequestValidator $validator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $email =  $body['email'] ?? null;
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;

        $validatorError = $this->validator->validate([
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

        $token = $this->userService->registerUser(
            $username,
            $email,
            $password,
        );

        return TaskWaveResult::from(
            JsonResult::from('User registered!', ['token' => $token->getToken()]),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
