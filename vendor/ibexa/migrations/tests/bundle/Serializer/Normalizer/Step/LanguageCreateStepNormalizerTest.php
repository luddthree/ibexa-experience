<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Language\Metadata;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\LanguageCreateStepNormalizer
 */
final class LanguageCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/language--create/language--create.yaml');

        $data = [
            $expectedStep = new LanguageCreateStep(
                new Metadata(
                    'eng-GB',
                    'English (United Kingdom)',
                    true
                ),
                [
                    new ReferenceDefinition(
                        'ref__2__language_id',
                        'language_id'
                    ),
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
        $source = self::loadFile(__DIR__ . '/language--create/language--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(LanguageCreateStep::class, $deserialized);
            $expectedStep = new LanguageCreateStep(
                new Metadata(
                    'eng-GB',
                    'English (United Kingdom)',
                    true
                ),
                [
                    new ReferenceDefinition(
                        'ref__2__language_id',
                        'language_id'
                    ),
                ]
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

class_alias(LanguageCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\LanguageCreateStepNormalizerTest');
