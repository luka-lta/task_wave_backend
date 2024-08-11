<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Email
{
    private function __construct(
        private readonly string $email
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new TaskWaveValidationFailureException(
                'Invalid email address',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    public static function from(string $email): self
    {
        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }
}
