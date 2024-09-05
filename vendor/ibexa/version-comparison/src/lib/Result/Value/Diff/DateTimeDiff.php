<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

use DateTime;

class DateTimeDiff extends AbstractDiffValue
{
    /** @var \DateTime|null */
    private $dateTime;

    public function __construct(string $status, ?DateTime $dateTime = null)
    {
        $this->status = $status;
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): ?DateTime
    {
        return $this->dateTime;
    }
}

class_alias(DateTimeDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\DateTimeDiff');
