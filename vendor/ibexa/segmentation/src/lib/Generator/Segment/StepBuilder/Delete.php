<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Generator\Segment\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentDeleteStep;

class Delete implements StepBuilderInterface
{
    /**
     * @param \Ibexa\Segmentation\Value\Segment $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        return new SegmentDeleteStep(
            new SegmentMatcher(
                $valueObject->id,
                $valueObject->identifier
            )
        );
    }
}
