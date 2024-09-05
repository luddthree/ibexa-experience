<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

use DateTimeImmutable;

abstract class AbstractEventDetail
{
    /** @var \DateTimeImmutable */
    private $timespanBegin;

    /** @var string */
    private $timespanDuration;

    public function __construct(
        DateTimeImmutable $timespanBegin,
        string $timespanDuration
    ) {
        $this->timespanBegin = $timespanBegin;
        $this->timespanDuration = $timespanDuration;
    }

    protected function getTimespanBegin(): DateTimeImmutable
    {
        return $this->timespanBegin;
    }

    protected function getTimespanDuration(): string
    {
        return $this->timespanDuration;
    }
}

class_alias(AbstractEventDetail::class, 'Ibexa\Platform\Personalization\Value\AbstractEventDetail');
