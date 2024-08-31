<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Task;

use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Api\Validator\RequestValidator;
use TaskWaveBackend\Service\TaskService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class CreateTaskAction extends TaskWaveAction
{
    public function __construct(
        private readonly TaskService      $taskService,
        private readonly RequestValidator $requestValidator
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        $title = $data['title'] ?? null;


        $validationResult = $this->requestValidator->validate([
            'ownerId' => $decodedToken->getUserId(),
            'title' => $title,
        ]);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->taskService->createTask(
            $decodedToken->getUserId(),
            $data['categoryId'] ?? null,
            $title,
            $data['description'] ?? null,
            $data['deadline'] ? new DateTimeImmutable($data['deadline']) : null,
            $data['priority'] ?? null,
            $data['status'] ?? null,
            $data['pinned'] ?? null
        );

        return TaskWaveResult::from(JsonResult::from('Task created successfully.'))->getResponse($response);
    }
}
