<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\SegmentGroup\StepBuilder;

use Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Delete;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Delete
 */
final class DeleteTest extends TestCase
{
    public function testBuild(): void
    {
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);

        $stepBuilder = new Delete();

        $step = $stepBuilder->build($segmentGroup);

        self::assertInstanceOf(SegmentGroupDeleteStep::class, $step);
    }
}
