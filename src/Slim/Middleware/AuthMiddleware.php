<?php

declare(strict_types=1);

namespace TaskWaveBackend\Slim\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TaskWaveBackend\Exception\TaskWaveAuthException;
use TaskWaveBackend\Service\JwtService;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtService $jwtService,
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader[0], $matches)) {
            throw new TaskWaveAuthException(
                'Authorization header not found or invalid.',
                StatusCodeInterface::STATUS_UNAUTHORIZED
            );
        }

        $token = $matches[1];

        $decoded = $this->jwtService->decodeJwt($token);

        $request = $request->withAttribute('jwt', $decoded);

        return $handler->handle($request);
    }
}
