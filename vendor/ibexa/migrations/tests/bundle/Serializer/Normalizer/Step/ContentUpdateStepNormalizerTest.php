<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\RemoteId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

final class ContentUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/content--update/content--update.yaml');

        $metadata = UpdateMetadata::createFromArray([
            'initialLanguageCode' => 'eng-GB',
            'creatorId' => null,
            'remoteId' => '__NEW_REMOTE_ID__',
            'alwaysAvailable' => true,
            'mainLanguageCode' => 'eng-FOO',
            'mainLocationId' => 45,
            'modificationDate' => '2020-12-28T13:20:00+00:00',
            'publishedDate' => '2020-12-28T13:21:00+00:00',
            'name' => '__NEW_NAME__',
            'ownerId' => '__NEW_OWNER_ID__',
        ]);

        $data = [
            new ContentUpdateStep(
                $metadata,
                new RemoteId('__REMOTE_ID__'),
                [
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'name',
                        'languageCode' => 'eng-GB',
                        'value' => '5 Patio Arrangements to Inspire Your Outdoor Room',
                    ]),
                ]
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content--update/content--update.yaml');

        $expectation = static function ($deserialized): void {
            $metadata = UpdateMetadata::createFromArray([
                'initialLanguageCode' => 'eng-GB',
                'creatorId' => null,
                'remoteId' => '__NEW_REMOTE_ID__',
                'alwaysAvailable' => true,
                'mainLanguageCode' => 'eng-FOO',
                'mainLocationId' => 45,
                'modificationDate' => '2020-12-28T13:20:00+00:00',
                'publishedDate' => '2020-12-28T13:21:00+00:00',
                'name' => '__NEW_NAME__',
                'ownerId' => '__NEW_OWNER_ID__',
            ]);

            $expectedStep = new ContentUpdateStep(
                $metadata,
                new LogicalAnd([
                    new RemoteId('__REMOTE_ID__'),
                    new Visibility(Visibility::VISIBLE),
                ]),
                [
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'name',
                        'languageCode' => 'eng-GB',
                        'value' => '5 Patio Arrangements to Inspire Your Outdoor Room',
                    ]),
                ]
            );

            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentUpdateStep::class, $deserialized);
            self::assertCount(1, $deserialized);
            [$deserializedObject] = $deserialized;

            self::assertEquals($expectedStep, $deserializedObject);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ContentUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentUpdateStepNormalizerTest');
