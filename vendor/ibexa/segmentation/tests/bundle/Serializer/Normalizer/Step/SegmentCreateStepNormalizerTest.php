<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Step;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentCreateStepNormalizer;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentCreateStepNormalizer
 */
final class SegmentCreateStepNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Step\SegmentCreateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $denormalizerMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $normalizerMock;

    protected function setUp(): void
    {
        $this->denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->normalizer = new SegmentCreateStepNormalizer();
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
        self::assertSame('create', $this->normalizer->getMode());
    }

    public function testGetHandledClassType(): void
    {
        self::assertSame(SegmentCreateStep::class, $this->normalizer->getHandledClassType());
    }

    public function testNormalizeStep(): void
    {
        $step = new SegmentCreateStep(
            $this->segment->identifier,
            $this->segment->name,
            new SegmentGroupMatcher(
                $this->segmentGroup->id,
            ),
        );

        $this->normalizerMock->expects(self::exactly(3))
            ->method('normalize')
            ->withConsecutive([
                self::identicalTo($step->getGroup()),
            ], [
                self::identicalTo($step->getReferences()),
            ], [
                self::identicalTo([]),
            ])
            ->willReturn(['foo'], ['bar'], ['alpha']);

        $data = $this->normalizer->normalize($step);

        self::assertIsArray($data);
        self::assertSame('segment', $data['type']);
        self::assertSame('create', $data['mode']);
        self::assertSame($this->segment->name, $data['name']);
        self::assertSame($this->segment->identifier, $data['identifier']);
        self::assertSame(['foo'], $data['group']);
        self::assertSame(['bar'], $data['references']);
        self::assertSame(['alpha'], $data['actions']);
    }

    public function testDenormalizeStep(): void
    {
        $data = [
            'name' => $this->segment->name,
            'identifier' => $this->segment->identifier,
            'group' => ['foo'],
            'references' => ['bar'],
            'actions' => ['alpha'],
        ];

        $this->denormalizerMock->expects(self::exactly(3))
            ->method('denormalize')
            ->withConsecutive(
                [self::identicalTo(['foo'])],
                [self::identicalTo(['bar'])],
                [self::identicalTo(['alpha'])],
            )
            ->willReturn(
                new SegmentGroupMatcher(0),
                [],
                [],
            );

        $step = $this->normalizer->denormalize($data, SegmentCreateStep::class);

        self::assertInstanceOf(SegmentCreateStep::class, $step);
        self::assertSame($this->segment->name, $step->getName());
        self::assertSame($this->segment->identifier, $step->getIdentifier());
    }
}
