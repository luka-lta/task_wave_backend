<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\User;

use Fig\Http\Message\StatusCodeInterface;
use TaskWaveBackend\Exception\TaskWaveValidationFailureException;

class Gender
{
    private const MALE = 'Male';
    private const FEMALE = 'Female';
    private const OTHER = 'Other';

    private const ALLOWED_GENDERS = [
        self::MALE,
        self::FEMALE,
        self::OTHER,
    ];

    private function __construct(
        private readonly string $gender
    ) {
        if (!in_array($gender, self::ALLOWED_GENDERS)) {
            throw new TaskWaveValidationFailureException(
                'Invalid gender',
                StatusCodeInterface::STATUS_BAD_REQUEST
            );
        }
    }

    public static function from(string $gender): self
    {
        return new self($gender);
    }

    public function toString(): string
    {
        return $this->gender;
    }
}
