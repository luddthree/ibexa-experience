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
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentCreateStepNormalizer
 */
final class SegmentCreateStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentCreateStep(
            'foo_identifier',
            'foo name',
            new SegmentGroupMatcher(
                42,
                'foo_group_identifier',
            ),
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment
            mode: create
            name: 'foo name'
            identifier: foo_identifier
            group:
                id: 42
                identifier: foo_group_identifier
            references: {  }

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment
            mode: create
            name: 'foo name'
            identifier: foo_identifier
            group: 
                id: 42
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentCreateStep::class, $step);
        self::assertSame('foo_identifier', $step->getIdentifier());
        self::assertSame('foo name', $step->getName());
        self::assertSame(42, $step->getGroup()->getId());
    }
}
