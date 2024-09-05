<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Step\UserGroupDeleteStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\UserGroupDeleteStepNormalizer
 */
final class UserGroupDeleteStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/user-group--delete/user-group--delete.yaml');

        $data = [
            new UserGroupDeleteStep(
                new Matcher('remoteId', '__remote_id__')
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/user-group--delete/user-group--delete.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(UserGroupDeleteStep::class, $deserialized);
            self::assertCount(1, $deserialized);

            [$step] = $deserialized;
            self::assertEquals(new Matcher('remoteId', '__remote_id__'), $step->match);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(UserGroupDeleteStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\UserGroupDeleteStepNormalizerTest');
