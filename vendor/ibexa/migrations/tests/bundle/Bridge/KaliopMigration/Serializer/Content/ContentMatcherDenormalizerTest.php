<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content\ContentMatcherDenormalizer;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content\ContentMatcherDenormalizer
 */
final class ContentMatcherDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content\ContentMatcherDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ContentMatcherDenormalizer();
    }

    public function testSupportsDenormalization(): void
    {
        self::assertFalse($this->denormalizer->supportsDenormalization(null, stdClass::class));
        self::assertTrue($this->denormalizer->supportsDenormalization(null, Criterion::class));
    }

    /**
     * @dataProvider provideForDenormalization
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        $result = $this->denormalizer->denormalize($data, Criterion::class);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @return iterable<string, array{
     *      array<mixed>,
     *      array<mixed>,
     * }>
     */
    public function provideForDenormalization(): iterable
    {
        $data = [
            'match' => [
                'parent_location_id' => '__parent_location_id__',
            ],
        ];

        $expectedResult = [
            'field' => 'parent_location_id',
            'value' => '__parent_location_id__',
        ];

        yield 'Match using "parent_location_id"' => [$data, $expectedResult];

        $data = [
            'match' => [
                'location_id' => '__location_id__',
            ],
        ];

        $expectedResult = [
            'field' => 'location_id',
            'value' => '__location_id__',
        ];

        yield 'Match using "location_id"' => [$data, $expectedResult];

        $data = [
            'match' => [
                'content_remote_id' => '__content_remote_id__',
            ],
        ];

        $expectedResult = [
            'field' => 'content_remote_id',
            'value' => '__content_remote_id__',
        ];

        yield 'Match using "content_remote_id" (data with "match" property)' => [$data, $expectedResult];

        $data = [
            'remote_id' => '__content_remote_id__',
        ];

        $expectedResult = [
            'field' => 'content_remote_id',
            'value' => '__content_remote_id__',
        ];

        yield 'Match using "content_remote_id" (data with "remote_id" property)' => [$data, $expectedResult];
    }
}

class_alias(ContentMatcherDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\Content\ContentMatcherDenormalizerTest');
