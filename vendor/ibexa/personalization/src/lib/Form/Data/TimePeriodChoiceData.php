<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

class TimePeriodChoiceData
{
    /** @var string */
    private $period;

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }
}

class_alias(TimePeriodChoiceData::class, 'Ibexa\Platform\Personalization\Form\Data\TimePeriodChoiceData');
