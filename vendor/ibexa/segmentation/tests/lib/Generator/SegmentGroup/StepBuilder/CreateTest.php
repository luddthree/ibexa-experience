<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\SegmentGroup\StepBuilder;

use Ibexa\Segmentation\Generator\Reference\SegmentGroupGenerator;
use Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Create;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\SegmentGroup\StepBuilder\Create
 */
final class CreateTest extends TestCase
{
    public function testBuild(): void
    {
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);

        $referenceGenerator = new SegmentGroupGenerator();
        $stepBuilder = new Create($referenceGenerator);

        $step = $stepBuilder->build($segmentGroup);

        self::assertInstanceOf(SegmentGroupCreateStep::class, $step);
    }
}
