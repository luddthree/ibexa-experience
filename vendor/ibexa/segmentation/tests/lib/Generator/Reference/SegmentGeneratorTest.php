<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\Reference;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Segmentation\Generator\Reference\SegmentGenerator;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\Reference\SegmentGenerator
 */
final class SegmentGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);
        $segment = new Segment([
            'id' => 2,
            'identifier' => 'test',
            'name' => 'test',
            'group' => $segmentGroup,
        ]);

        $generator = new SegmentGenerator();

        $references = $generator->generate($segment);
        self::assertContainsOnlyInstancesOf(ReferenceDefinition::class, $references);
        self::assertCount(1, $references);
        self::assertSame('segment_id', $references[0]->getType());
    }
}
