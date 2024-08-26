<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Password;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\PasswordResetService;
use TaskWaveBackend\Service\UserService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;
use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\Password;

class UpdatePasswordAction extends TaskWaveAction
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RequestValidator $requestValidator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;
        $resetToken = $body['resetToken'] ?? null;

        $validationResult = $this->requestValidator->validate([
            'email' => $email,
            'password' => $password,
            'resetToken' => $resetToken,
        ]);

        $email = Email::from($email);
        $password = Password::fromPlain($password);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->userService->updatePassword($email, $password, $resetToken);

        return TaskWaveResult::from(
            JsonResult::from('Password updated!'),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
