<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\StepExecutor\ReferenceDefinition\Segment;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Ibexa\Segmentation\StepExecutor\ReferenceDefinition\Segment\SegmentIdResolver;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\ReferenceDefinition\Segment\SegmentIdResolver
 */
final class SegmentIdResolverTest extends TestCase
{
    /** @var \Ibexa\Segmentation\StepExecutor\ReferenceDefinition\Segment\SegmentIdResolver */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = new SegmentIdResolver();
    }

    public function testGetHandledType(): void
    {
        self::assertSame('segment_id', $this->resolver::getHandledType());
    }

    public function testResolve(): void
    {
        $referenceDefinition = new ReferenceDefinition('test', 'segment_id');
        $segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'Test',
        ]);
        $segment = new Segment([
            'id' => 2,
            'identifier' => 'test',
            'name' => 'Test',
            'group' => $segmentGroup,
        ]);

        $reference = $this->resolver->resolve($referenceDefinition, $segment);
        self::assertInstanceOf(Reference::class, $reference);
        self::assertSame('test', $reference->getName());
        self::assertSame(2, $reference->getValue());
    }
}
