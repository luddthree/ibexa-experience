<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Migration\ValueObject\ObjectStateGroup\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ObjectStateGroupCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ObjectStateGroupCreateStepNormalizer
 */
final class ObjectStateGroupCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/object-state-group--create/object-state-group--create.yaml');

        $objectState = new ObjectStateGroup([
            'identifier' => 'state_group_1',
            'mainLanguageCode' => 'eng-GB',
            'names' => ['eng-GB' => 'Object State Group 1 name'],
            'descriptions' => ['eng-GB' => 'Object State Group 1 description'],
        ]);

        $data = [
            new ObjectStateGroupCreateStep(
                CreateMetadata::createFromApi($objectState)
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/object-state-group--create/object-state-group--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ObjectStateGroupCreateStep::class, $deserialized);

            $metadata = CreateMetadata::createFromArray([
                'identifier' => 'state_group_1',
                'mainTranslation' => 'eng-GB',
                'translations' => [
                    'eng-GB' => [
                        'name' => 'Object State Group 1 name',
                        'description' => 'Object State Group 1 description',
                    ],
                ],
            ]);

            $expectedStep = new ObjectStateGroupCreateStep(
                $metadata
            );

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

class_alias(ObjectStateGroupCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ObjectStateGroupCreateStepNormalizerTest');
