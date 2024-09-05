<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ServiceCallStepNormalizer
 */
final class ServiceCallStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/service-call--execute/service-call.yaml');

        $data = [
            new ServiceCallExecuteStep(
                '__foo_service__',
                [],
                '__foo_method__',
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/service-call--execute/service-call.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ServiceCallExecuteStep::class, $deserialized);
            self::assertCount(1, $deserialized);

            /** @var \Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep $step */
            $step = $deserialized[0];
            self::assertSame('__foo_service__', $step->service);
            self::assertSame('__foo_method__', $step->method);
            self::assertSame([], $step->arguments);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ServiceCallStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ServiceCallStepNormalizerTest');
