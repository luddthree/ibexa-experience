<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Segment;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\MatcherNormalizer;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\MatcherNormalizer
 */
final class MatcherNormalizerTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\MatcherNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new MatcherNormalizer();

        $this->segmentSetUp();
    }

    public function testDenormalize(): void
    {
        $data = [
            'id' => $this->segment->id,
            'identifier' => $this->segment->identifier,
        ];

        $matcher = $this->normalizer->denormalize($data, SegmentMatcher::class);

        self::assertInstanceOf(SegmentMatcher::class, $matcher);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->normalizer->supportsDenormalization([], stdClass::class));
        self::assertTrue($this->normalizer->supportsDenormalization([], SegmentMatcher::class));
    }

    public function testNormalizeWithId(): void
    {
        $matcher = new SegmentMatcher(
            $this->segment->id,
            $this->segment->identifier
        );

        $data = $this->normalizer->normalize($matcher, SegmentMatcher::class);

        self::assertIsArray($data);
        self::assertArrayHasKey('id', $data);
        self::assertSame($this->segment->id, $data['id']);
        self::assertArrayHasKey('identifier', $data);
        self::assertSame($this->segment->identifier, $data['identifier']);
    }

    public function testSupportsNormalization(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization((object)[]));
        self::assertTrue($this->normalizer->supportsNormalization(new SegmentMatcher(
            $this->segment->id,
            $this->segment->identifier,
        )));
    }
}
