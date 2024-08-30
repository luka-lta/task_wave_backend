<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Task;

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
        $categoryId = (int)$data['categoryId'] ?? null;
        $description = $data['description'] ?? null;
        $deadline = $data['deadline'] ?? null;
        $priority = $data['priority'] ?? null;
        $status = $data['status'] ?? null;
        $pinned = (bool)$data['pinned'] ?? null;

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
            $categoryId,
            $title,
            $description,
            $deadline,
            $priority,
            $status,
            $pinned
        );

        return TaskWaveResult::from(JsonResult::from('Task created successfully.'))->getResponse($response);
    }
}
