<?php

declare(strict_types=1);

namespace TaskWaveBackend\Slim;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TaskWaveBackend\Exception\TaskWaveException;
use TaskWaveBackend\Value\ErrorResult;
use TaskWaveBackend\Value\TaskWaveResult;
use Throwable;

abstract class TaskWaveAction
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            $response = $this->execute($request, $response);
        } catch (TaskWaveException $exception) {
            $result = TaskWaveResult::from(
                ErrorResult::from($exception),
                $exception->getCode()
            );

            return $result->getResponse($response);
        } catch (Throwable $exception) {
            $result = TaskWaveResult::from(
                ErrorResult::from($exception),
                500
            );

            return $result->getResponse($response);
        }

        return $response;
    }

    abstract protected function execute(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface;
}
