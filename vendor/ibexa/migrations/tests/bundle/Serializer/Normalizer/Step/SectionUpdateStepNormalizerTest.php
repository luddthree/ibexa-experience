<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Section\Matcher;
use Ibexa\Migration\ValueObject\Section\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\SectionUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\SectionUpdateStepNormalizer
 */
final class SectionUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/section--update/section--update.yaml');

        $data = [
            new SectionUpdateStep(
                new Matcher('identifier', '__IDENTIFIER__'),
                UpdateMetadata::createFromArray([
                    'identifier' => '__NEW_IDENTIFIER__',
                    'name' => '__NAME__',
                ]),
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
        $source = self::loadFile(__DIR__ . '/section--update/section--update.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(SectionUpdateStep::class, $deserialized);
            self::assertCount(1, $deserialized);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(SectionUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\SectionUpdateStepNormalizerTest');
