<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentUpdateStepNormalizer;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentUpdateStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentUpdateStepNormalizer
 */
final class SegmentUpdateStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentUpdateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentUpdateStepNormalizer();
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
        self::assertSame('update', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentUpdateStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentUpdateStep(
            new SegmentMatcher(
                $this->segment->id,
                $this->segment->identifier,
            ),
            $this->segment->identifier,
            $this->segment->name
        );

        $this->normalizerMock->expects(self::exactly(2))
            ->method('normalize')
            ->withConsecutive(
                [self::identicalTo($step->getMatcher())],
                [self::identicalTo([])],
            )
            ->willReturn('normalizedData');

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment', $data['type']);
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
            ->willReturn(new SegmentMatcher(
                $this->segment->id,
                $this->segment->identifier,
            ));

        $step = $this->normalizer->denormalize($data, SegmentUpdateStep::class);

        self::assertInstanceOf(SegmentUpdateStep::class, $step);
    }
}
