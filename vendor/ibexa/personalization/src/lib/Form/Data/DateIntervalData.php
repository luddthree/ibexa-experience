<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

class DateIntervalData
{
    /** @var string|null */
    private $dateInterval;

    /** @var int|null */
    private $endDate;

    public function __construct(
        ?string $dateInterval = null,
        ?int $endDate = null
    ) {
        $this->dateInterval = $dateInterval;
        $this->endDate = $endDate;
    }

    public function getDateInterval(): ?string
    {
        return $this->dateInterval;
    }

    public function setDateInterval(string $dateInterval): self
    {
        $this->dateInterval = $dateInterval;

        return $this;
    }

    public function getEndDate(): ?int
    {
        return $this->endDate;
    }

    public function setEndDate(?int $endDate = null): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}

class_alias(DateIntervalData::class, 'Ibexa\Platform\Personalization\Form\Data\DateIntervalData');
