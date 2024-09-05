<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupCreateStepNormalizer;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupCreateStepNormalizer
 */
final class SegmentGroupCreateStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentGroupCreateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentGroupCreateStepNormalizer();
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
        self::assertSame('create', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentGroupCreateStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentGroupCreateStep(
            $this->segment->identifier,
            $this->segment->name,
        );

        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with($step->references)
            ->willReturn(['foo']);

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment_group', $data['type']);
        self::assertSame('create', $data['mode']);
        self::assertSame($this->segment->name, $data['name']);
        self::assertSame($this->segment->identifier, $data['identifier']);
    }

    public function testDenormalizeStep(): void
    {
        $data = [
            'name' => $this->segmentGroup->name,
            'identifier' => $this->segmentGroup->identifier,
            'references' => ['foo'],
        ];

        $this->denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(self::identicalTo(['foo']))
            ->willReturn([]);

        $step = $this->normalizer->denormalize($data, SegmentGroupCreateStep::class);

        self::assertInstanceOf(SegmentGroupCreateStep::class, $step);
        self::assertSame($this->segment->name, $step->getName());
        self::assertSame($this->segment->identifier, $step->getIdentifier());
    }
}
