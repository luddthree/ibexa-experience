<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupUpdateStepNormalizer
 */
final class SegmentGroupUpdateStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentGroupUpdateStep(
            new SegmentGroupMatcher(43),
            'foo_identifier',
            'foo name',
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: update
            name: 'foo name'
            identifier: foo_identifier
            matcher:
                id: 43

            YAML,
            $yaml,
        );

        $step = new SegmentGroupUpdateStep(
            new SegmentGroupMatcher(null, 'foo_identifier'),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: update
            matcher:
                identifier: foo_identifier

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment_group
            mode: update
            name: 'foo name'
            identifier: foo_identifier
            matcher: 
                id: 43
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupUpdateStep::class, $step);
        self::assertSame('foo_identifier', $step->getIdentifier());
        self::assertSame('foo name', $step->getName());
        self::assertSame(43, $step->getMatcher()->getId());
        self::assertNull($step->getMatcher()->getIdentifier());

        $yaml = <<<YAML
            type: segment_group
            mode: update
            matcher: 
                identifier: foo_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupUpdateStep::class, $step);
        self::assertNull($step->getIdentifier());
        self::assertNull($step->getName());
        self::assertNull($step->getMatcher()->getId());
        self::assertSame('foo_identifier', $step->getMatcher()->getIdentifier());
    }
}
