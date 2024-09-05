<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\ModelBuild;

use DateTimeImmutable;

final class BuildReport
{
    private int $numberOfItems;

    private string $state;

    private ?string $taskUuid;

    private ?DateTimeImmutable $queueTime;

    private ?DateTimeImmutable $startTime;

    private ?DateTimeImmutable $finishTime;

    public function __construct(
        int $numberOfItems,
        string $state,
        ?string $taskUuid = null,
        ?DateTimeImmutable $queueTime = null,
        ?DateTimeImmutable $startTime = null,
        ?DateTimeImmutable $finishTime = null
    ) {
        $this->numberOfItems = $numberOfItems;
        $this->state = $state;
        $this->taskUuid = $taskUuid;
        $this->queueTime = $queueTime;
        $this->startTime = $startTime;
        $this->finishTime = $finishTime;
    }

    public function getNumberOfItems(): int
    {
        return $this->numberOfItems;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getTaskUuid(): ?string
    {
        return $this->taskUuid;
    }

    public function getQueueTime(): ?DateTimeImmutable
    {
        return $this->queueTime;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getFinishTime(): ?DateTimeImmutable
    {
        return $this->finishTime;
    }

    /**
     * @param array{
     *     'queueTime': ?string,
     *     'startTime': ?string,
     *     'finishTime': ?string,
     *     'numberOfItems': int,
     *     'taskUuid': ?string,
     *     'state': string,
     * } $properties
     *
     * @throws \Exception
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['numberOfItems'],
            $properties['state'],
            $properties['taskUuid'] ?? null,
            isset($properties['queueTime']) ? new DateTimeImmutable($properties['queueTime']) : null,
            isset($properties['startTime']) ? new DateTimeImmutable($properties['startTime']) : null,
            isset($properties['finishTime']) ? new DateTimeImmutable($properties['finishTime']) : null,
        );
    }
}
