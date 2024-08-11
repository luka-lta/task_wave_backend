<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Register\Action;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\RegisterService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\ErrorResult;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class RegisterAction extends TaskWaveAction
{
    public function __construct(
        private readonly RegisterService $registerService,
        private readonly RequestValidator $validator,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

//        $validatorError = $this->validator->validate([
//            'email' => $body['email'],
//            'username' => $body['username'],
//            'password' => $body['password'],
//        ]);

        $this->registerService->register(
            $body['email'],
            $body['username'],
            $body['password'],
        );

        return TaskWaveResult::from(
            JsonResult::from('User registered!'),
            StatusCodeInterface::STATUS_CREATED
        )->getResponse($response);
    }
}
