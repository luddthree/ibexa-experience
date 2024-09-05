<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Metadata;

use DateTimeImmutable;

/**
 * Represents an already executed migration.
 * The migration might be not available anymore.
 */
final class ExecutedMigration
{
    /** @var string */
    private $name;

    /** @var \DateTimeImmutable|null */
    private $executedAt;

    /**
     * Seconds.
     *
     * @var float|null
     */
    public $executionTime;

    public function __construct(string $name, ?DateTimeImmutable $executedAt = null, ?float $executionTime = null)
    {
        $this->name = $name;
        $this->executedAt = $executedAt;
        $this->executionTime = $executionTime;
    }

    public function getExecutionTime(): ?float
    {
        return $this->executionTime;
    }

    public function getExecutedAt(): ?DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class_alias(ExecutedMigration::class, 'Ibexa\Platform\Migration\Metadata\ExecutedMigration');
