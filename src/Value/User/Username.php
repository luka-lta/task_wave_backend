<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Username
{
    private function __construct(
        private readonly string $username,
    ) {
        if (empty($username)) {
            throw new TaskWaveValidationFailureException('Username cannot be empty');
        }

        if (strlen($username) < 3) {
            throw new TaskWaveValidationFailureException('Username must be at least 3 characters long');
        }

        if (strlen($username) > 32) {
            throw new TaskWaveValidationFailureException('Username must be at most 20 characters long');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new TaskWaveValidationFailureException(
                'Username must contain only letters, numbers, and underscores'
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
