<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value;

use DateTimeImmutable;

class PasswordReset
{
    private function __construct(
        private readonly string            $email,
        private readonly string            $token,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $expiredAt,
    ) {
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['email'],
            $data['token'],
            new DateTimeImmutable($data['created_at']),
            new DateTimeImmutable($data['expired_at']),
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }
}
