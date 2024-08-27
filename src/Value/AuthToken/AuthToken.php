<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\AuthToken;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use TaskWaveBackend\Exception\TaskWaveInvalidTokenException;
use TaskWaveBackend\Value\User\User;

class AuthToken
{
    public function __construct(
        private readonly string $token,
    )
    {
    }

    public static function generateToken(User $user): AuthToken
    {
        $payload = [
            'iss' => 'taskwave',
            'email' => $user->getEmail()->toString(),
            'username' => $user->getUsername()->toString(),
            'sub' => $user->getUserId(),
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

        return new self($token);
    }

    public static function decodeToken(string $token): DecodedToken
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
        } catch (BeforeValidException) {
            throw new TaskWaveInvalidTokenException(
                'An error occurred on validate token',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }

        return DecodedToken::fromArray((array)$decoded);
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
