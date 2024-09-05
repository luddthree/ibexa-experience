<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentDeleteStepNormalizer;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentDeleteStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentDeleteStepNormalizer
 */
final class SegmentDeleteStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentDeleteStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentDeleteStepNormalizer();
        $this->normalizer->setDenormalizer($this->denormalizerMock);
        $this->normalizer->setNormalizer($this->normalizerMock);

        $this->segmentSetUp();
    }

    public function testGetType(): void
    {
        self::assertSame('segment', $this->normalizer->getType());
    }

    public function testGetMode(): void
    {
        self::assertSame('delete', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentDeleteStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentDeleteStep(
            new SegmentMatcher(
                $this->segment->id,
                $this->segment->identifier,
            )
        );

        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(self::identicalTo($step->getMatcher()))
            ->willReturn('normalizedData');

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment', $data['type']);
        self::assertSame('delete', $data['mode']);
        self::assertSame('normalizedData', $data['matcher']);
    }

    public function testDenormalizeStep(): void
    {
        $data = [
            'matcher' => [
                'id' => $this->segment->id,
                'identifier' => $this->segment->identifier,
            ],
        ];

        $segmentMatcher = new SegmentMatcher(
            $this->segment->id,
            $this->segment->identifier,
        );
        $this->denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(self::identicalTo($data['matcher']))
            ->willReturn($segmentMatcher);

        $step = $this->normalizer->denormalize($data, SegmentDeleteStep::class);

        self::assertInstanceOf(SegmentDeleteStep::class, $step);
        self::assertSame($segmentMatcher, $step->getMatcher());
    }
}
