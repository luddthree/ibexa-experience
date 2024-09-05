<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\StepExecutor\ReferenceDefinition\SegmentGroup;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Segmentation\StepExecutor\ReferenceDefinition\SegmentGroup\SegmentGroupIdResolver;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\ReferenceDefinition\SegmentGroup\SegmentGroupIdResolver
 */
final class SegmentGroupIdResolverTest extends TestCase
{
    /** @var \Ibexa\Segmentation\StepExecutor\ReferenceDefinition\SegmentGroup\SegmentGroupIdResolver */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = new SegmentGroupIdResolver();
    }

    public function testGetHandledType(): void
    {
        self::assertSame('segment_group_id', $this->resolver::getHandledType());
    }

    public function testResolve(): void
    {
        $referenceDefinition = new ReferenceDefinition('test', 'segment_group_id');
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'Test',
        ]);

        $reference = $this->resolver->resolve($referenceDefinition, $segmentGroup);
        self::assertInstanceOf(Reference::class, $reference);
        self::assertSame('test', $reference->getName());
        self::assertSame(1, $reference->getValue());
    }
}
