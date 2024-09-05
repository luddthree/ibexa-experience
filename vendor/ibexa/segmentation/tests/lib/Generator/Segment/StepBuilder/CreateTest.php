<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\Segment\StepBuilder;

use Ibexa\Segmentation\Generator\Reference\SegmentGenerator;
use Ibexa\Segmentation\Generator\Segment\StepBuilder\Create;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\Generator\Segment\StepBuilder\Create
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
        $segment = new Segment([
            'id' => 2,
            'identifier' => 'test',
            'name' => 'test',
            'group' => $segmentGroup,
        ]);

        $referenceGenerator = new SegmentGenerator();
        $stepBuilder = new Create($referenceGenerator);

        $step = $stepBuilder->build($segment);

        self::assertInstanceOf(SegmentCreateStep::class, $step);
    }
}
