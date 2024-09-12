<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Role
{
    // Mögliche Rollen
    private const ADMIN = 'Admin';
    private const EDITOR = 'Editor';
    private const READER = 'Reader';
    private const USER = 'User';

    private function __construct(
        private readonly int $roleId,
        private readonly string $role,
    ) {
        if (!in_array($role, [self::ADMIN, self::EDITOR, self::READER, self::USER])) {
            throw new TaskWaveValidationFailureException(
                'Invalid role',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    public static function from(int $roleId, string $role): self
    {
        return new self($roleId, $role);
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::EDITOR;
    }

    public function isReader(): bool
    {
        return $this->role === self::READER;
    }

    public function isUser(): bool
    {
        return $this->role === self::USER;
    }

    public function canRead(): bool
    {
        return in_array($this->role, [self::ADMIN, self::EDITOR, self::READER, self::USER]);
    }

    public function canWrite(): bool
    {
        return in_array($this->role, [self::ADMIN, self::EDITOR]);
    }

    public function canDelete(): bool
    {
        return $this->role === self::ADMIN;
    }
}
