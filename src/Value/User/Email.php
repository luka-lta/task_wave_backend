<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use InvalidArgumentException;

class Email
{
    private function __construct(
        private readonly string $email
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
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
