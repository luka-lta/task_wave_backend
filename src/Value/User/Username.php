<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Username
{
    private function __construct(
        private readonly string $username,
    ) {
        if (empty($username)) {
            throw new TaskWaveValidationFailureException(
                'Username cannot be empty',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if (strlen($username) < 3) {
            throw new TaskWaveValidationFailureException(
                'Username must be at least 3 characters long',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if (strlen($username) > 32) {
            throw new TaskWaveValidationFailureException(
                'Username must be at most 20 characters long',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new TaskWaveValidationFailureException(
                'Username must contain only letters, numbers, lines and underscores',
                StatusCodeInterface::STATUS_BAD_REQUEST,
            );
        }
    }

    public static function from(string $username): self
    {
        return new self($username);
    }

    public function toString(): string
    {
        return $this->username;
    }
}
