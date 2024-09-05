<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupCreateStepNormalizer
 */
final class SegmentGroupCreateStepNormalizerTest extends AbstractSerializerTestCase
{
    public function testSerialization(): void
    {
        $step = new SegmentGroupCreateStep(
            'foo_identifier',
            'foo name',
        );

        $yaml = $this->serializer->serialize($step, 'yaml');

        self::assertSame(
            <<<YAML
            type: segment_group
            mode: create
            name: 'foo name'
            identifier: foo_identifier
            references: {  }

            YAML,
            $yaml,
        );
    }

    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            type: segment_group
            mode: create
            name: 'foo name'
            identifier: foo_identifier
            YAML;

        $step = $this->serializer->deserialize($yaml, StepInterface::class, 'yaml');

        self::assertInstanceOf(SegmentGroupCreateStep::class, $step);
        self::assertSame('foo_identifier', $step->getIdentifier());
        self::assertSame('foo name', $step->getName());
    }
}
