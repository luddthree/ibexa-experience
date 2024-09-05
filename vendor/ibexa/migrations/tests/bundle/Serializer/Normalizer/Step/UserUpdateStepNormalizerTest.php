<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\UserUpdateStep;
use Ibexa\Migration\ValueObject\User\UpdateMetadata;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\UserUpdateStepNormalizer
 */
final class UserUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/user--update/user--update.yaml');

        $data = [
            new UserUpdateStep(
                UpdateMetadata::createFromArray([
                    'email' => 'anonymous@link.invalid',
                    'enabled' => true,
                    'password' => '__PASSWORD__',
                ]),
                new Criterion\UserLogin('__LOGIN__'),
                [
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'first_name',
                        'languageCode' => 'eng-GB',
                        'value' => '__FIRST_NAME__',
                    ]),
                    Field::createFromArray([
                        'fieldDefIdentifier' => 'last_name',
                        'languageCode' => 'eng-GB',
                        'value' => '__LAST_NAME__',
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
        $source = self::loadFile(__DIR__ . '/user--update/user--update.yaml');
        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(UserUpdateStep::class, $deserialized);

            self::assertCount(1, $deserialized);

            /** @var \Ibexa\Migration\ValueObject\Step\UserUpdateStep $step */
            $step = $deserialized[0];
            self::assertInstanceOf(UpdateMetadata::class, $step->metadata);
            self::assertSame('anonymous@link.invalid', $step->metadata->email);
            self::assertSame('__PASSWORD__', $step->metadata->password);
            self::assertTrue($step->metadata->enabled);

            self::assertInstanceOf(Criterion::class, $step->criterion);

            self::assertCount(2, $step->fields);
            self::assertContainsOnlyInstancesOf(Field::class, $step->fields);
            [
                $fieldOne,
                $fieldTwo,
            ] = $step->fields;

            self::assertSame('first_name', $fieldOne->fieldDefIdentifier);
            self::assertSame('eng-GB', $fieldOne->languageCode);
            self::assertIsString($fieldOne->value);
            self::assertSame('__FIRST_NAME__', $fieldOne->value);

            self::assertSame('last_name', $fieldTwo->fieldDefIdentifier);
            self::assertSame('eng-GB', $fieldTwo->languageCode);
            self::assertIsString($fieldTwo->value);
            self::assertSame('__LAST_NAME__', $fieldTwo->value);
        };

        yield [
            $source,
            $expectation,
        ];

        $source = self::loadFile(__DIR__ . '/user--update/user--update-incomplete.yaml');
        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(UserUpdateStep::class, $deserialized);

            self::assertCount(1, $deserialized);

            [$step] = $deserialized;
            self::assertInstanceOf(UpdateMetadata::class, $step->metadata);
            self::assertNull($step->metadata->email);
            self::assertNull($step->metadata->password);
            self::assertNull($step->metadata->enabled);

            self::assertInstanceOf(Criterion::class, $step->criterion);

            self::assertCount(0, $step->fields);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(UserUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\UserUpdateStepNormalizerTest');
