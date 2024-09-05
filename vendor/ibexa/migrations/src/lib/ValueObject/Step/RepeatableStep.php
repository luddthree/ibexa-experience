<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class RepeatableStep implements StepInterface
{
    /** @var iterable<\Ibexa\Migration\ValueObject\Step\StepInterface> */
    private iterable $steps;

    /**
     * @param iterable<\Ibexa\Migration\ValueObject\Step\StepInterface> $steps
     */
    public function __construct(iterable $steps)
    {
        $this->steps = $steps;
    }

    /**
     * @return iterable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     */
    public function getSteps(): iterable
    {
        return $this->steps;
    }
}
