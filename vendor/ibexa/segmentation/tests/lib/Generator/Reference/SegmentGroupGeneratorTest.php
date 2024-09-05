<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\Reference;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Segmentation\Generator\Reference\SegmentGroupGenerator;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\Reference\SegmentGroupGenerator
 */
final class SegmentGroupGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);

        $generator = new SegmentGroupGenerator();

        $references = $generator->generate($segmentGroup);
        self::assertContainsOnlyInstancesOf(ReferenceDefinition::class, $references);
        self::assertCount(1, $references);
        self::assertSame('segment_group_id', $references[0]->getType());
    }
}
