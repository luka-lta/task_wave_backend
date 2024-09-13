<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Login\Action;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\LoginService;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;
use TaskWaveBackend\Value\User\Email;

class LoginAction extends TaskWaveAction
{
    public function __construct(
        private readonly LoginService     $loginService,
        private readonly RequestValidator $validator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $email =  $body['email'] ?? null;
        $password = $body['password'] ?? null;

        $validatorError = $this->validator->validate([
            'email' => $email,
            'password' => $password,
        ]);

        if ($validatorError) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validatorError]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $token = $this->loginService->loginUser($email, $password);

        return TaskWaveResult::from(
            JsonResult::from(
                'Login successfull',
                [
                    'token' => $token->getToken()
                ]
            )
        )->getResponse($response);
    }
}
