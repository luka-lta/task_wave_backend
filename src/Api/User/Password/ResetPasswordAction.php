<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\User\Password;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\PasswordResetService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;
use TaskWaveBackend\Value\User\Email;

class ResetPasswordAction extends TaskWaveAction
{
    public function __construct(
        private readonly PasswordResetService $service,
        private readonly RequestValidator $requestValidator,
    )
    {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $email = $request->getAttribute('email') ?? null;

        $validationResult = $this->requestValidator->validate([
            'email' => $email,
        ]);

        $email = Email::from($email);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->service->createPasswordResetToken($email);

        return TaskWaveResult::from(
            JsonResult::from('Password reset link sent!'),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
