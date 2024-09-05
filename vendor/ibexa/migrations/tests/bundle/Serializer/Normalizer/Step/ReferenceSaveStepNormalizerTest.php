<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\ReferenceSaveStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ReferenceSaveStepNormalizer
 */
final class ReferenceSaveStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expectedStep = new ReferenceSaveStep(
            'file.yaml'
        );

        yield [
            [$expectedStep],
            self::loadFile(__DIR__ . '/reference--save/reference--save.yaml'),
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/reference--save/reference--save.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ReferenceSaveStep::class, $deserialized);

            $expectedStep = new ReferenceSaveStep(
                'file.yaml'
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

class_alias(ReferenceSaveStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ReferenceSaveStepNormalizerTest');
