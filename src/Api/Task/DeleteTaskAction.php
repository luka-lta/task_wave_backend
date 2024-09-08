<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Task;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Service\TaskService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class DeleteTaskAction extends TaskWaveAction
{
    public function __construct(
        private readonly TaskService $taskService
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $taskId = (int) $request->getAttribute('taskId');
        $ownerId = DecodedToken::fromArray($request->getAttribute('decodedToken'))->getUserId();

        $this->taskService->deleteTask($taskId, $ownerId);

        return TaskWaveResult::from(JsonResult::from('Task deleted successfully'))->getResponse($response);
    }
}
