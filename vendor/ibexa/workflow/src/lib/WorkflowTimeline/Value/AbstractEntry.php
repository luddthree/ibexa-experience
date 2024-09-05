<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\WorkflowTimeline\Value;

use DateTimeInterface;

abstract class AbstractEntry
{
    /** @var \DateTimeInterface */
    private $date;

    /**
     * @param \DateTimeInterface $date
     */
    public function __construct(DateTimeInterface $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return string
     */
    abstract public function getIdentifier(): string;
}

class_alias(AbstractEntry::class, 'EzSystems\EzPlatformWorkflow\WorkflowTimeline\Value\AbstractEntry');
