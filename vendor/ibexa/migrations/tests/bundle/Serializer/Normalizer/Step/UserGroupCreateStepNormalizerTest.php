<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\UserGroupCreateStep;
use Ibexa\Migration\ValueObject\UserGroup\CreateMetadata;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\UserGroupCreateStepNormalizer
 */
final class UserGroupCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/user-group--create/normalize/user-group--create.yaml');

        $data = [
            new UserGroupCreateStep(
                $this->buildSampleCreateMetadata(),
                $this->buildSampleFields(),
                [
                    'Anonymous',
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
        $source = self::loadFile(__DIR__ . '/user-group--create/denormalize/user-group--create.yaml');

        $expectation = function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertCount(1, $deserialized);
            self::assertContainsOnlyInstancesOf(UserGroupCreateStep::class, $deserialized);

            [$step] = $deserialized;
            self::assertEquals(
                $this->buildSampleCreateMetadata(),
                $step->metadata
            );

            self::assertEquals(
                $this->buildSampleFields(),
                $step->fields
            );
        };

        yield [
            $source,
            $expectation,
        ];
    }

    private function buildSampleCreateMetadata(): CreateMetadata
    {
        return CreateMetadata::createFromArray([
            'mainLanguage' => 'eng-GB',
            'parentGroupId' => 42,
            'remoteId' => '__remote_id__',
        ]);
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Content\Field[]
     */
    private function buildSampleFields(): array
    {
        return [
            Field::createFromArray([
                'fieldDefIdentifier' => 'name',
                'languageCode' => 'eng-GB',
                'value' => '__user_group_name__',
            ]),
        ];
    }
}

class_alias(UserGroupCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\UserGroupCreateStepNormalizerTest');
