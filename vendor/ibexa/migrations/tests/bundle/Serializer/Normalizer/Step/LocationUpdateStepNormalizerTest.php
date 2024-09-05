<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Location\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\LocationUpdateStepNormalizer
 */
final class LocationUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/location--update/location--update.yaml');

        $data = [
            new LocationUpdateStep(
                UpdateMetadata::createFromArray([
                    'remoteId' => 'f3e90596361e31d496d4026eb624c983',
                    'priority' => 0,
                    'sortField' => Location::SORT_FIELD_PRIORITY,
                    'sortOrder' => Location::SORT_ORDER_ASC,
                ]),
                new Matcher(
                    Matcher::LOCATION_REMOTE_ID,
                    'f3e90596361e31d496d4026eb624c983'
                ),
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/location--update/location--update.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(LocationUpdateStep::class, $deserialized);
            $expectedStep = new LocationUpdateStep(
                UpdateMetadata::createFromArray([
                    'remoteId' => 'f3e90596361e31d496d4026eb624c983',
                    'priority' => 0,
                    'sortField' => Location::SORT_FIELD_PRIORITY,
                    'sortOrder' => Location::SORT_ORDER_ASC,
                ]),
                new Matcher(
                    Matcher::LOCATION_REMOTE_ID,
                    'f3e90596361e31d496d4026eb624c983'
                ),
            );

            self::assertCount(1, $deserialized);
            $deserializedObject = $deserialized[0];

            self::assertEquals($expectedStep, $deserializedObject);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(LocationUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\LocationUpdateStepNormalizerTest');
