<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class PopularityDurationChoiceData
{
    private ?string $duration;

    public function __construct(?string $duration = null)
    {
        $this->duration = $duration;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration = null): self
    {
        $this->duration = $duration;

        return $this;
    }
}
