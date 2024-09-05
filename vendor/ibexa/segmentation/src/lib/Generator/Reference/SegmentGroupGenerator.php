<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\AbstractReferenceGenerator;
use Ibexa\Migration\Generator\Reference\ReferenceMetadata;
use Ibexa\Segmentation\StepExecutor\ReferenceDefinition\SegmentGroup\SegmentGroupIdResolver;
use Ibexa\Segmentation\Value\SegmentGroup;
use Webmozart\Assert\Assert;

final class SegmentGroupGenerator extends AbstractReferenceGenerator
{
    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceMetadata[]
     */
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref__segment_group__id', SegmentGroupIdResolver::getHandledType()),
        ];
    }

    /**
     * @param \Ibexa\Segmentation\Value\SegmentGroup $valueObject
     */
    public function generate(ValueObject $valueObject): array
    {
        Assert::isInstanceOf($valueObject, SegmentGroup::class);

        return $this->generateReferences('segment_group', $valueObject->name);
    }
}
