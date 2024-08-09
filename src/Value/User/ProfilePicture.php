<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

class ProfilePicture
{
    private function __construct(
        private readonly string $profilePicturePath
    ) {}

    public static function fromString(string $profilePicturePath): self
    {
        return new self($profilePicturePath);
    }

    public function toString(): string
    {
        return $this->profilePicturePath;
    }
}
