<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Segment;

use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\SegmentMatcher;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\MatcherNormalizer
 */
final class MatcherNormalizerTest extends AbstractSerializerTestCase
{
    /**
     * @dataProvider provideForSerialization
     */
    public function testSerialization(SegmentMatcher $matcher, string $expected): void
    {
        $yaml = $this->serializer->serialize($matcher, 'yaml');

        self::assertSame(
            $expected,
            $yaml,
        );
    }

    /**
     * @return iterable<array{\Ibexa\Segmentation\Value\SegmentMatcher, non-empty-string}>
     */
    public function provideForSerialization(): iterable
    {
        yield [
            new SegmentMatcher(42),
            <<<YAML
            id: 42

            YAML,
        ];

        yield [
            new SegmentMatcher(null, 'foo_identifier'),
            <<<YAML
            identifier: foo_identifier

            YAML,
        ];

        yield [
            new SegmentMatcher(42, 'foo_identifier'),
            <<<YAML
            id: 42
            identifier: foo_identifier

            YAML,
        ];
    }

    /**
     * @dataProvider provideForDeserialization
     */
    public function testDeserialization(string $yaml, SegmentMatcher $expected): void
    {
        $segment = $this->serializer->deserialize($yaml, SegmentMatcher::class, 'yaml');

        self::assertEquals($expected, $segment);
    }

    /**
     * @return iterable<array{non-empty-string, \Ibexa\Segmentation\Value\SegmentMatcher}>
     */
    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            id: 42
            YAML,
            new SegmentMatcher(42),
        ];

        yield [
            <<<YAML
            identifier: foo_identifier
            YAML,
            new SegmentMatcher(null, 'foo_identifier'),
        ];

        yield [
            <<<YAML
            id: 42
            identifier: foo_identifier
            YAML,
            new SegmentMatcher(42, 'foo_identifier'),
        ];
    }
}
