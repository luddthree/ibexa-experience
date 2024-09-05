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
use Ibexa\Segmentation\Generator\Reference\SegmentGenerator as ReferenceSegmentGenerator;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;

class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Segmentation\Generator\Reference\SegmentGenerator */
    private $referenceSegmentGenerator;

    public function __construct(
        ReferenceSegmentGenerator $referenceSegmentGenerator
    ) {
        $this->referenceSegmentGenerator = $referenceSegmentGenerator;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Segment $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        $references = $this->referenceSegmentGenerator->generate($valueObject);

        return new SegmentCreateStep(
            $valueObject->identifier,
            $valueObject->name,
            new SegmentGroupMatcher($valueObject->group->id, $valueObject->group->identifier),
            $references,
        );
    }
}
