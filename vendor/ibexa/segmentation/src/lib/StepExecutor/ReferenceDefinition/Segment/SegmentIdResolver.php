<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor\ReferenceDefinition\Segment;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Segmentation\Value\Segment;
use Webmozart\Assert\Assert;

final class SegmentIdResolver implements SegmentResolverInterface
{
    public static function getHandledType(): string
    {
        return 'segment_id';
    }

    /**
     * @param \Ibexa\Segmentation\Value\Segment $valueObject
     */
    public function resolve(ReferenceDefinition $referenceDefinition, ValueObject $valueObject): Reference
    {
        Assert::isInstanceOf($valueObject, Segment::class);
        $value = $valueObject->id;
        Assert::notNull(
            $value,
            'Content object does not contain an ID. Make sure to reload Content object if persisted.'
        );

        return Reference::create(
            $referenceDefinition->getName(),
            $value,
        );
    }
}
