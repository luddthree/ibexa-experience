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
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupDeleteStepNormalizer
 */
final class SegmentGroupDeleteStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentGroupDeleteStep(
            new SegmentGroupMatcher(42),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: delete
            matcher:
                id: 42

            YAML,
            $yaml,
        );

        $step = new SegmentGroupDeleteStep(
            new SegmentGroupMatcher(null, 'foo_identifier'),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: delete
            matcher:
                identifier: foo_identifier

            YAML,
            $yaml,
        );

        $step = new SegmentGroupDeleteStep(
            new SegmentGroupMatcher(42, 'foo_identifier'),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: delete
            matcher:
                id: 42
                identifier: foo_identifier

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment_group
            mode: delete
            matcher: 
                id: 43
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupDeleteStep::class, $step);
        self::assertSame(43, $step->getMatcher()->getId());
        self::assertNull($step->getMatcher()->getIdentifier());

        $yaml = <<<YAML
            type: segment_group
            mode: delete
            matcher: 
                identifier: foo_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupDeleteStep::class, $step);
        self::assertNull($step->getMatcher()->getId());
        self::assertSame('foo_identifier', $step->getMatcher()->getIdentifier());

        $yaml = <<<YAML
            type: segment_group
            mode: delete
            matcher: 
                id: 43
                identifier: foo_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupDeleteStep::class, $step);
        self::assertSame(43, $step->getMatcher()->getId());
        self::assertSame('foo_identifier', $step->getMatcher()->getIdentifier());
    }
}
