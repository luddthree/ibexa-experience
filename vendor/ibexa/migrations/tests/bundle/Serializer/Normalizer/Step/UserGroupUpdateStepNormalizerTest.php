<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\UserGroupUpdateStep;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\UserGroupUpdateStepNormalizer
 */
final class UserGroupUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/user-group--update/normalize/user-group--update.yaml');

        $data = [
            new UserGroupUpdateStep(
                $this->buildSampleUpdateMetadata(),
                new Matcher('id', 42),
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
        $source = self::loadFile(__DIR__ . '/user-group--update/denormalize/user-group--update.yaml');

        $expectation = function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertCount(1, $deserialized);
            self::assertContainsOnlyInstancesOf(UserGroupUpdateStep::class, $deserialized);

            [$step] = $deserialized;
            self::assertEquals(
                $this->buildSampleUpdateMetadata(),
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

    private function buildSampleUpdateMetadata(): UpdateMetadata
    {
        return UpdateMetadata::createFromArray([
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
