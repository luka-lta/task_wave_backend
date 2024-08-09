<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

class Password
{
    private function __construct(
        private readonly string $password
    ) {
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public static function fromPlain(string $plainPassword): self
    {
        return new self(password_hash($plainPassword, PASSWORD_BCRYPT));
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    public function toString(): string
    {
        return $this->password;
    }
}
