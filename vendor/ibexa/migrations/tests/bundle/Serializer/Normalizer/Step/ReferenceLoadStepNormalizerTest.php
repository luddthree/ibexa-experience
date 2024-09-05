<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\ReferenceLoadStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ReferenceLoadStepNormalizer
 */
final class ReferenceLoadStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        yield [
            [
                new ReferenceLoadStep(
                    'file.yaml'
                ),
            ],
            self::loadFile(__DIR__ . '/reference--load/reference--load.yaml'),
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/reference--load/reference--load.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ReferenceLoadStep::class, $deserialized);

            $expectedStep = [
                new ReferenceLoadStep(
                    'file.yaml'
                ),
            ];

            self::assertEquals($expectedStep, $deserialized);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ReferenceLoadStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ReferenceLoadStepNormalizerTest');
