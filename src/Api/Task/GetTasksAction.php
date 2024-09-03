<?php

declare(strict_types=1);

namespace TaskWaveBackend\Api\Task;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Exception\TaskWaveTaskNotFoundException;
use TaskWaveBackend\Service\TaskService;
use TaskWaveBackend\Slim\TaskWaveAction;
use TaskWaveBackend\Value\AuthToken\DecodedToken;
use TaskWaveBackend\Value\JsonResult;
use TaskWaveBackend\Value\TaskWaveResult;

class GetTasksAction extends TaskWaveAction
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    protected function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $decodedToken = DecodedToken::fromArray($request->getAttribute('jwt'));

        try {
            $tasks = $this->taskService->getAllTasksByOwnerId($decodedToken->getUserId());

            $allTasks = [];

            foreach ($tasks as $task) {
                $allTasks[] = $task->toArray();
            }
        } catch (TaskWaveTaskNotFoundException $e) {
            return TaskWaveResult::from(JsonResult::from($e->getMessage(), $e->getCode()))->getResponse($response);
        }

        return TaskWaveResult::from(JsonResult::from('Tasks found', [
            'tasks' => $allTasks,
        ]))->getResponse($response);
    }
}
