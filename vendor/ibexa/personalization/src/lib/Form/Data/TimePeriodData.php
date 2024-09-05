<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class TimePeriodData
{
    private ?string $period;

    private ?string $quantifier;

    public function __construct(
        ?string $period = null,
        ?string $quantifier = null
    ) {
        $this->period = $period;
        $this->quantifier = $quantifier;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function getQuantifier(): ?string
    {
        return $this->quantifier;
    }

    public function setQuantifier(?string $quantifier = null): void
    {
        $this->quantifier = $quantifier;
    }
}

class_alias(TimePeriodData::class, 'Ibexa\Platform\Personalization\Form\Data\RelevantHistoryData');
