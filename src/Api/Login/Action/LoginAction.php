<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Login\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;
use TaskWaveBackend\Value\User\Email;

class LoginAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $email = Email::from($data['email']);
        $password = $data['password'];

        $token = $this->userService->loginUser($email, $password);

        return TaskWaveResult::from(
            JsonResult::from(
                'Login successfull',
                [
                    'token' => $token
                ]
            )
        )->getResponse($response);
    }
}
