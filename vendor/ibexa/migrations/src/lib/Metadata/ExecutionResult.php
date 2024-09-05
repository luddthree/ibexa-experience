<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Metadata;

use DateTimeImmutable;
use Throwable;

final class ExecutionResult
{
    /**
     * Seconds.
     *
     * @var float|null
     */
    private $time;

    /** @var float|null */
    private $memory;

    /** @var bool */
    private $error = false;

    /** @var \Throwable|null */
    private $exception;

    /** @var \DateTimeImmutable|null */
    private $executedAt;

    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setExecutedAt(DateTimeImmutable $executedAt): void
    {
        $this->executedAt = $executedAt;
    }

    public function getExecutedAt(): ?DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function setTime(float $time): void
    {
        $this->time = $time;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function setMemory(float $memory): void
    {
        $this->memory = $memory;
    }

    public function getMemory(): ?float
    {
        return $this->memory;
    }

    public function setError(bool $error, ?Throwable $exception = null): void
    {
        $this->error = $error;
        $this->exception = $exception;
    }

    public function hasError(): bool
    {
        return $this->error;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }
}

class_alias(ExecutionResult::class, 'Ibexa\Platform\Migration\Metadata\ExecutionResult');
