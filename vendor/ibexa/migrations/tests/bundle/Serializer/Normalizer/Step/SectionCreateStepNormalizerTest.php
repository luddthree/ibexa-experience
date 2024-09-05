<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Section\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\SectionCreateStepNormalizer
 */
final class SectionCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/section--create/section--create.yaml');

        $objectState = new Section([
            'identifier' => 'section_1',
            'name' => 'Section 1 name',
        ]);

        $data = [
            new SectionCreateStep(
                CreateMetadata::createFromApi($objectState),
                [
                    new ReferenceDefinition(
                        'ref__section__1__section_id',
                        'section_id',
                    ),
                ],
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/section--create/section--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(SectionCreateStep::class, $deserialized);

            $metadata = CreateMetadata::createFromArray([
                'identifier' => 'section_1',
                'name' => 'Section 1 name',
            ]);

            $expectedStep = new SectionCreateStep(
                $metadata,
                [
                    new ReferenceDefinition(
                        'ref__section__1__section_id',
                        'section_id',
                    ),
                ],
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

class_alias(SectionCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\SectionCreateStepNormalizerTest');
