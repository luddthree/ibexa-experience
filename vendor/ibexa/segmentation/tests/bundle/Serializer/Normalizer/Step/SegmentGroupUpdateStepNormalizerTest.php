<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupUpdateStepNormalizer;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupUpdateStepNormalizer
 */
final class SegmentGroupUpdateStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupUpdateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentGroupUpdateStepNormalizer();
        $this->normalizer->setDenormalizer($this->denormalizerMock);
        $this->normalizer->setNormalizer($this->normalizerMock);

        $this->segmentSetUp();
    }

    public function testGetType(): void
    {
        self::assertSame('segment_group', $this->normalizer->getType());
    }

    public function testGetMode(): void
    {
        self::assertSame('update', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentGroupUpdateStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentGroupUpdateStep(
            new SegmentGroupMatcher(
                $this->segmentGroup->id,
                $this->segmentGroup->identifier,
            ),
            $this->segmentGroup->identifier,
            $this->segmentGroup->name
        );

        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(self::identicalTo($step->getMatcher()))
            ->willReturn('normalizedData');

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment_group', $data['type']);
        self::assertSame('update', $data['mode']);
        self::assertSame($this->segment->identifier, $data['identifier']);
        self::assertSame($this->segment->name, $data['name']);
        self::assertSame('normalizedData', $data['matcher']);
    }

    public function testDenormalizeStep(): void
    {
        $data = [
            'matcher' => [
                'id' => $this->segment->id,
                'identifier' => $this->segment->identifier,
            ],
            'identifier' => $this->segment->identifier,
            'name' => $this->segment->name,
        ];

        $this->denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with($data['matcher'])
            ->willReturn(new SegmentGroupMatcher(
                $this->segmentGroup->id,
                $this->segmentGroup->identifier,
            ));

        $step = $this->normalizer->denormalize($data, SegmentGroupUpdateStep::class);

        self::assertInstanceOf(SegmentGroupUpdateStep::class, $step);
    }
}
