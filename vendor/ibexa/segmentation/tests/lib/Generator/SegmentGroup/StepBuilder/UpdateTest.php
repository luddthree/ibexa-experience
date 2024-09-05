<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\SegmentGroup\StepBuilder;

use Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Update;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Update
 */
final class UpdateTest extends TestCase
{
    public function testBuild(): void
    {
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);

        $stepBuilder = new Update();

        $step = $stepBuilder->build($segmentGroup);

        self::assertInstanceOf(SegmentGroupUpdateStep::class, $step);
    }
}
