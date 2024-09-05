<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\SegmentGroup;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\SegmentGroup\MatcherNormalizer;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\SegmentGroup\MatcherNormalizer
 */
final class MatcherNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\SegmentGroup\MatcherNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->segmentationMock = $this->createMock(SegmentationServiceInterface::class);
        $this->normalizer = new MatcherNormalizer();

        $this->segmentSetUp();
    }

    public function testDenormalize(): void
    {
        $data = [
            'id' => $this->segmentGroup->id,
            'identifier' => $this->segmentGroup->identifier,
        ];

        $matcher = $this->normalizer->denormalize($data, SegmentGroupMatcher::class);

        self::assertInstanceOf(SegmentGroupMatcher::class, $matcher);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->normalizer->supportsDenormalization([], SegmentGroupMatcher::class));
    }

    public function testNormalizeWithId(): void
    {
        $matcher = new SegmentGroupMatcher(
            $this->segmentGroup->id,
            $this->segmentGroup->identifier,
        );

        $data = $this->normalizer->normalize($matcher, SegmentGroupMatcher::class);

        self::assertIsArray($data);
        self::assertArrayHasKey('id', $data);
        self::assertSame($this->segmentGroup->id, $data['id']);
        self::assertArrayHasKey('identifier', $data);
        self::assertSame($this->segment->identifier, $data['identifier']);
    }

    public function testSupportsNormalization(): void
    {
        self::assertTrue(
            $this->normalizer->supportsNormalization(
                new SegmentGroupMatcher(
                    $this->segmentGroup->id,
                    $this->segmentGroup->identifier,
                )
            )
        );
    }
}
