<?php

declare(strict_types=1);

namespace TaskWaveBackend\Value\Todo;

use DateTimeImmutable;

class TaskTimeFrame
{
    private function __construct(
        private readonly ?DateTimeImmutable $deadline,
        private readonly ?DateTimeImmutable $startedOn,
        private readonly ?DateTimeImmutable $finishedOn,
    ) {
    }

    public static function from(
        ?DateTimeImmutable $deadline,
        ?DateTimeImmutable $startedOn,
        ?DateTimeImmutable $finishedOn
    ): self {
        return new self(
            $deadline,
            $startedOn,
            $finishedOn,
        );
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['deadline'] ? new DateTimeImmutable($data['deadline']) : null,
            $data['started_on'] ? new DateTimeImmutable($data['started_on']) : null,
            $data['finished_on'] ? new DateTimeImmutable($data['finished_on']) : null,
        );
    }

    public function getDeadline(): ?DateTimeImmutable
    {
        return $this->deadline;
    }

    public function getStartedOn(): ?DateTimeImmutable
    {
        return $this->startedOn;
    }

    public function getFinishedOn(): ?DateTimeImmutable
    {
        return $this->finishedOn;
    }
}
