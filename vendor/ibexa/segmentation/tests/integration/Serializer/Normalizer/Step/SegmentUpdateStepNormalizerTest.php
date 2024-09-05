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
use Ibexa\Segmentation\Value\Step\SegmentUpdateStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentUpdateStepNormalizer
 */
final class SegmentUpdateStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentUpdateStep(
            new SegmentMatcher(43),
            'foo_identifier',
            'foo name',
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment
            mode: update
            name: 'foo name'
            identifier: foo_identifier
            matcher:
                id: 43

            YAML,
            $yaml,
        );

        $step = new SegmentUpdateStep(
            new SegmentMatcher(null, 'bar_identifier'),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment
            mode: update
            matcher:
                identifier: bar_identifier

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment
            mode: update
            name: 'foo name'
            identifier: foo_identifier
            matcher: 
                id: 43
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentUpdateStep::class, $step);
        self::assertSame('foo_identifier', $step->getIdentifier());
        self::assertSame('foo name', $step->getName());
        self::assertSame(43, $step->getMatcher()->getId());

        $yaml = <<<YAML
            type: segment
            mode: update
            matcher: 
                identifier: bar_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentUpdateStep::class, $step);
        self::assertNull($step->getIdentifier());
        self::assertNull($step->getName());
        self::assertSame('bar_identifier', $step->getMatcher()->getIdentifier());
    }
}
