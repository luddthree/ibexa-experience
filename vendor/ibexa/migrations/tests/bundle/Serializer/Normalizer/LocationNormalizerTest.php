<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer;

use Ibexa\Bundle\Migration\Serializer\Normalizer\LocationNormalizer;
use Ibexa\Migration\ValueObject\Content\Location;
use PHPUnit\Framework\TestCase;

final class LocationNormalizerTest extends TestCase
{
    /**
     * @dataProvider provideForNormalization
     *
     * @param array<string, scalar|null> $source
     * @param array<string, scalar|null> $expectation
     */
    public function testNormalizeWithParentLocationId(array $source, array $expectation): void
    {
        $location = Location::createFromArray($source);
        $normalizer = new LocationNormalizer();
        $data = $normalizer->normalize($location);

        self::assertSame($expectation, $data);
    }

    /**
     * @return iterable<string, array{array<string, scalar|null>, array<string, scalar|null>}>
     */
    public function provideForNormalization(): iterable
    {
        $baseExpectation = [
            'parentLocationId' => null,
            'parentLocationRemoteId' => null,
            'locationRemoteId' => null,
            'hidden' => null,
            'sortField' => null,
            'sortOrder' => null,
            'priority' => null,
        ];

        yield 'parentLocationId only' => [
            [
                'parentLocationId' => 1,
            ],
            array_merge($baseExpectation, [
                'parentLocationId' => 1,
            ]),
        ];

        yield 'parentLocationRemoteId only' => [
            [
                'parentLocationRemoteId' => '1',
            ],
            array_merge($baseExpectation, [
                'parentLocationRemoteId' => '1',
            ]),
        ];

        yield 'both parentLocationRemoteId & parentLocationId' => [
            [
                'parentLocationId' => 1,
                'parentLocationRemoteId' => '1',
            ],
            array_merge($baseExpectation, [
                'parentLocationId' => 1,
                'parentLocationRemoteId' => '1',
            ]),
        ];
    }

    public function testDenormalizeThrowsWhenMissingParentLocation(): void
    {
        $normalizer = new LocationNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Location should have `locationParentId` or `locationParentRemoteId` defined');
        $normalizer->denormalize([], '');
    }

    public function testDenormalizeWithParentLocationId(): void
    {
        $normalizer = new LocationNormalizer();
        $location = $normalizer->denormalize([
            'parentLocationId' => 1,
        ], '');

        self::assertInstanceOf(Location::class, $location);
        self::assertSame(1, $location->parentLocationId);
        self::assertNull($location->parentLocationRemoteId);
    }

    public function testDenormalizeWithParentLocationRemoteId(): void
    {
        $normalizer = new LocationNormalizer();
        $location = $normalizer->denormalize([
            'parentLocationRemoteId' => '1',
        ], '');

        self::assertInstanceOf(Location::class, $location);
        self::assertSame('1', $location->parentLocationRemoteId);
        self::assertNull($location->parentLocationId);
    }

    public function testDenormalizeWithBothParentLocationRemoteIdAndId(): void
    {
        $normalizer = new LocationNormalizer();
        $location = $normalizer->denormalize([
            'parentLocationRemoteId' => '1',
            'parentLocationId' => 1,
        ], '');

        self::assertInstanceOf(Location::class, $location);
        self::assertSame('1', $location->parentLocationRemoteId);
        self::assertSame(1, $location->parentLocationId);
    }
}

class_alias(LocationNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\LocationNormalizerTest');
