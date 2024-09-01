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

class EditTaskAction extends TaskWaveAction
{
    public function __construct(
        private readonly TaskService $taskService,
        private readonly RequestValidator $requestValidator
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $taskId = (int)$request->getAttribute('taskId');
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));
        $data = $request->getParsedBody();

        $categoryId = $data['categoryId'] ?? null;
        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;
        $deadline = isset($data['deadline']) ? new DateTimeImmutable($data['deadline']) : null;
        $priority = $data['priority'] ?? null;
        $status = $data['status'] ?? null;
        $pinned = (bool)$data['pinned'] ?? null;
        $startedOn = isset($data['startedOn']) ? new DateTimeImmutable($data['startedOn']) : null;
        $finishedOn = isset($data['finishedOn']) ? new DateTimeImmutable($data['finishedOn']) : null;

        $validationResult = $this->requestValidator->validate([
            'title' => $title,
        ]);

        if ($validationResult) {
            return TaskWaveResult::from(
                JsonResult::from('Invalid input', ['error' => $validationResult]),
                StatusCodeInterface::STATUS_BAD_REQUEST
            )->getResponse($response);
        }

        $this->taskService->editTask(
            $decodedToken->getUserId(),
            $taskId,
            $categoryId,
            $title,
            $description,
            $deadline,
            $priority,
            $status,
            $pinned,
            $startedOn,
            $finishedOn
        );

        return TaskWaveResult::from(
            JsonResult::from('Task edited'),
        )->getResponse($response);
    }
}
