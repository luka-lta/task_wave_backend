<?php

declare(strict_types=1);

namespace TaskWaveBackend\Service;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use TaskWaveBackend\Exception\TaskWaveInvalidTokenException;
use TaskWaveBackend\Value\User\User;

class JwtService
{
    public function generateJwt(User $user): string
    {
        $payload = [
            'iss' => 'taskwave',
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'sub' => $user->getUserId(),
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    public function decodeJwt(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));

            if ($decoded->exp < time()) {
                throw new TaskWaveInvalidTokenException(
                    'Token expired',
                    StatusCodeInterface::STATUS_UNAUTHORIZED
                );
            }

            if ($decoded->iss !== 'taskwave') {
                throw new TaskWaveInvalidTokenException(
                    'Invalid token issuer',
                    StatusCodeInterface::STATUS_UNAUTHORIZED
                );
            }

            if (!isset($decoded->email) || !isset($decoded->username)) {
                throw new TaskWaveInvalidTokenException(
                    'Required claims missing',
                    StatusCodeInterface::STATUS_UNAUTHORIZED
                );
            }

            if ($decoded->iat > time()) {
                throw new TaskWaveInvalidTokenException(
                    'Invalid token issued at time',
                    StatusCodeInterface::STATUS_UNAUTHORIZED
                );
            }
        } catch (Exception) {
            throw new TaskWaveInvalidTokenException(
                'An error occurred on validate token',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        return (array) $decoded;
    }
}
