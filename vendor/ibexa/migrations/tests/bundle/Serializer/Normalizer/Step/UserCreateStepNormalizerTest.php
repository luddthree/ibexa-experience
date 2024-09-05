<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\UserCreateStep;
use Ibexa\Migration\ValueObject\User\CreateMetadata;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Ibexa\Tests\Migration\Doubles\DummyPasswordGenerator;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\UserCreateStepNormalizer
 */
final class UserCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    /**
     * @return iterable<array{UserCreateStep[], string}>
     */
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/user--create/normalize/user--create.yaml');

        $users = [
            self::createUserStep(),
        ];

        yield [
            $users,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/user--create/denormalize/user--create.yaml');
        $expectedStep = self::createUserStep();

        $expectation = static function ($deserialized) use ($expectedStep): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(UserCreateStep::class, $deserialized);
            self::assertCount(1, $deserialized);
            [$deserializedObject] = $deserialized;

            self::assertEquals($expectedStep, $deserializedObject);
        };

        yield [
            $source,
            $expectation,
        ];
    }

    private static function createUserStep(): UserCreateStep
    {
        return new UserCreateStep(
            CreateMetadata::createFromArray([
                'login' => '__LOGIN__',
                'email' => 'anonymous@link.invalid',
                'password' => DummyPasswordGenerator::PASSWORD,
                'enabled' => true,
                'mainLanguage' => 'eng-GB',
                'contentType' => 'user',
            ]),
            [
                '__GROUP_REMOTE_ID__',
            ],
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
            ],
            [
                new ReferenceDefinition(
                    'ref__user_anonymous',
                    'user_id',
                ),
            ]
        );
    }
}

class_alias(UserCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\UserCreateStepNormalizerTest');
