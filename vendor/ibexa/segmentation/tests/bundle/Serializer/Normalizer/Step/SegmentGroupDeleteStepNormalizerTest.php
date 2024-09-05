<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupDeleteStepNormalizer;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupDeleteStepNormalizer
 */
final class SegmentGroupDeleteStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupDeleteStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentGroupDeleteStepNormalizer();
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
        self::assertSame('delete', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentGroupDeleteStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentGroupDeleteStep(
            new SegmentGroupMatcher(
                $this->segmentGroup->id,
                $this->segmentGroup->identifier,
            )
        );

        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(self::identicalTo($step->getMatcher()))
            ->willReturn('normalizedData');

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment_group', $data['type']);
        self::assertSame('delete', $data['mode']);
        self::assertSame('normalizedData', $data['matcher']);
    }

    public function testDenormalizeStep(): void
    {
        $data = [
            'matcher' => [
                'id' => $this->segmentGroup->id,
                'identifier' => $this->segmentGroup->identifier,
            ],
        ];

        $this->denormalizerMock->expects(TestCase::once())
            ->method('denormalize')
            ->with($data['matcher'])
            ->willReturn(new SegmentGroupMatcher(
                $this->segmentGroup->id,
                $this->segmentGroup->identifier,
            ));

        $step = $this->normalizer->denormalize($data, SegmentGroupDeleteStep::class);

        self::assertInstanceOf(SegmentGroupDeleteStep::class, $step);
        self::assertSame($this->segmentGroup->id, $step->getMatcher()->getId());
        self::assertSame($this->segmentGroup->identifier, $step->getMatcher()->getIdentifier());
    }
}
