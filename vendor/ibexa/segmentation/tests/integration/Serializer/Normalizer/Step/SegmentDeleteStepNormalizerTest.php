<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentDeleteStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentDeleteStepNormalizer
 */
final class SegmentDeleteStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentDeleteStep(
            new SegmentMatcher(43),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment
            mode: delete
            matcher:
                id: 43

            YAML,
            $yaml,
        );

        $step = new SegmentDeleteStep(
            new SegmentMatcher(null, 'foo_identifier'),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment
            mode: delete
            matcher:
                identifier: foo_identifier

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment
            mode: delete
            matcher: 
                id: 43
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentDeleteStep::class, $step);
        self::assertInstanceOf(SegmentMatcher::class, $step->getMatcher());
        self::assertSame(43, $step->getMatcher()->getId());

        $yaml = <<<YAML
            type: segment
            mode: delete
            matcher: 
                identifier: foo_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentDeleteStep::class, $step);
        self::assertInstanceOf(SegmentMatcher::class, $step->getMatcher());
        self::assertSame('foo_identifier', $step->getMatcher()->getIdentifier());
    }
}
