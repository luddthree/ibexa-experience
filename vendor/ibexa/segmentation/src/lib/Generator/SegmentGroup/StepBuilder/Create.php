<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Generator\Reference\SegmentGroupGenerator;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;

class Create implements StepBuilderInterface
{
    /** @var \Ibexa\Segmentation\Generator\Reference\SegmentGroupGenerator */
    private $referenceSegmentGroupGenerator;

    public function __construct(
        SegmentGroupGenerator $referenceSegmentGroupGenerator
    ) {
        $this->referenceSegmentGroupGenerator = $referenceSegmentGroupGenerator;
    }

    /**
     * @param \Ibexa\Segmentation\Value\SegmentGroup $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        $references = $this->referenceSegmentGroupGenerator->generate($valueObject);

        return new SegmentGroupCreateStep(
            $valueObject->identifier,
            $valueObject->name,
            $references
        );
    }
}
