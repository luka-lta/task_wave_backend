<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value;

use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TaskWaveBackend\Slim\ResultInterface;

class TaskWaveResult
{
    private function __construct(
        private readonly ResultInterface $result,
        private readonly int             $status,
    ) {
        if ($status < 100 || $status > 599) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }
    }

    public static function from(
        ResultInterface $result,
        int             $status = StatusCodeInterface::STATUS_OK,
    ): TaskWaveResult {
        return new self($result, $status);
    }

    public function getResponse(ResponseInterface $response): ResponseInterface
    {
        $result = $this->result->toArray();
        $result = array_merge(['status' => $this->status], $result);

        $response->getBody()->write(json_encode($result));

        return $response
            ->withStatus($this->status)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
