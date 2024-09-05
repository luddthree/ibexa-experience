<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup\ContentTypeGroupCreateMetadataDenormalizer;
use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup\ContentTypeGroupCreateMetadataDenormalizer
 */
final class ContentTypeGroupCreateMetadataDenormalizerTest extends TestCase
{
    protected function setUp(): void
    {
        ClockMock::register(ContentTypeGroupCreateMetadataDenormalizer::class);
        ClockMock::register(CreateMetadata::class);
    }

    public function testDenormalize(): void
    {
        $variants = $this->provideDenormalizationData();

        foreach ($variants as $variant) {
            [
                $data,
                $expected,
            ] = $variant;

            $denormalizer = new ContentTypeGroupCreateMetadataDenormalizer();
            $denormalized = $denormalizer->denormalize($data, CreateMetadata::class);
            self::assertEquals($expected, $denormalized);
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>, \Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata}>
     */
    public function provideDenormalizationData(): iterable
    {
        yield 'Identifier only' => [
            [
                'identifier' => 'foo',
            ],
            CreateMetadata::createFromArray([
                'identifier' => 'foo',
            ]),
        ];

        yield 'With Date' => [
            [
                'identifier' => 'foo',
                'creationDate' => '2021-04-01 00:42:00',
            ],
            CreateMetadata::createFromArray([
                'identifier' => 'foo',
                'creationDate' => '2021-04-01 00:42:00',
            ]),
        ];

        yield 'With Creator ID' => [
            [
                'identifier' => 'foo',
                'creatorId' => 42,
            ],
            CreateMetadata::createFromArray([
                'identifier' => 'foo',
                'creatorId' => 42,
            ]),
        ];
    }
}

class_alias(ContentTypeGroupCreateMetadataDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Denormalizer\ContentTypeGroup\ContentTypeGroupCreateMetadataDenormalizerTest');
