<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\AuthToken;

use TaskWaveBackend\Value\User\Email;
use TaskWaveBackend\Value\User\Username;

class DecodedToken
{
    private function __construct(
        private readonly int $userId,
        private readonly string $iss,
        private readonly Email $email,
        private readonly Username $username,
        private readonly int $iat,
        private readonly int $exp,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['sub'],
            $data['iss'],
            Email::from($data['email']),
            Username::from($data['username']),
            $data['iat'],
            $data['exp'],
        );
    }

    public function toArray(): array
    {
        return [
            'sub' => $this->userId,
            'iss' => $this->iss,
            'email' => $this->email->toString(),
            'username' => $this->username->toString(),
            'iat' => $this->iat,
            'exp' => $this->exp,
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getIss(): string
    {
        return $this->iss;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getIat(): int
    {
        return $this->iat;
    }

    public function getExp(): int
    {
        return $this->exp;
    }
}
