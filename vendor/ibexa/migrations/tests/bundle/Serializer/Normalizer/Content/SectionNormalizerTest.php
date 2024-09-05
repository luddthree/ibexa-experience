<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Content;

use Ibexa\Bundle\Migration\Serializer\Normalizer\Content\SectionNormalizer;
use Ibexa\Migration\ValueObject\Content\Metadata\Section;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Content\SectionNormalizer
 */
final class SectionNormalizerTest extends TestCase
{
    public function testSupportsDenormalization(): void
    {
        $normalizer = new SectionNormalizer();

        self::assertTrue($normalizer->supportsDenormalization(null, Section::class));
        self::assertFalse($normalizer->supportsDenormalization(null, stdClass::class));
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new SectionNormalizer();

        self::assertTrue($normalizer->supportsNormalization(new Section(1, 'foo')));
        self::assertFalse($normalizer->supportsNormalization(new stdClass()));
    }

    public function testNormalization(): void
    {
        $normalizer = new SectionNormalizer();

        $result = $normalizer->normalize(new Section(1, 'foo'));
        self::assertSame([
            'id' => 1,
            'identifier' => 'foo',
        ], $result);

        $result = $normalizer->normalize(new Section(null, 'foo'));
        self::assertSame([
            'identifier' => 'foo',
        ], $result);
    }

    public function testDenormalization(): void
    {
        $normalizer = new SectionNormalizer();
        $section = $normalizer->denormalize(1, Section::class);
        self::assertSame(1, $section->getId());
        self::assertNull($section->getIdentifier());

        $section = $normalizer->denormalize(['id' => 1], Section::class);
        self::assertSame(1, $section->getId());
        self::assertNull($section->getIdentifier());

        $section = $normalizer->denormalize(['identifier' => 'foo'], Section::class);
        self::assertNull($section->getId());
        self::assertSame('foo', $section->getIdentifier());

        $section = $normalizer->denormalize(['id' => 1, 'identifier' => 'foo'], Section::class);
        self::assertSame(1, $section->getId());
        self::assertSame('foo', $section->getIdentifier());
    }
}

class_alias(SectionNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Content\SectionNormalizerTest');
