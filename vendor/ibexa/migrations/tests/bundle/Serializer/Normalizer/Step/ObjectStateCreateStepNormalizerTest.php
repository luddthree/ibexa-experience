<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Migration\ValueObject\ObjectState\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ObjectStateCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ObjectStateCreateStepNormalizer
 */
final class ObjectStateCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/object-state--create/normalize/object-state--create.yaml');

        $objectState = new ObjectState([
            'identifier' => 'state_1',
            'mainLanguageCode' => 'eng-GB',
            'objectStateGroup' => new ObjectStateGroup(['id' => 2]),
            'names' => ['eng-GB' => 'Object State 1 name'],
            'descriptions' => ['eng-GB' => 'Object State 1 description'],
        ]);

        $data = [
            new ObjectStateCreateStep(
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
        $source = self::loadFile(__DIR__ . '/object-state--create/denormalize/object-state--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ObjectStateCreateStep::class, $deserialized);

            $metadata = CreateMetadata::createFromArray([
                'identifier' => 'state_1',
                'mainTranslation' => 'eng-GB',
                'objectStateGroup' => 2,
                'priority' => false,
                'translations' => [
                    'eng-GB' => [
                        'name' => 'Object State 1 name',
                        'description' => 'Object State 1 description',
                    ],
                ],
            ]);

            $expectedStep = new ObjectStateCreateStep(
                $metadata
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

class_alias(ObjectStateCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ObjectStateCreateStepNormalizerTest');
