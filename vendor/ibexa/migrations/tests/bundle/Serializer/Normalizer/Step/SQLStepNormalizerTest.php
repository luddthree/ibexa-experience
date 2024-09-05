<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Sql\Query;
use Ibexa\Migration\ValueObject\Step\SQLExecuteStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\SQLStepNormalizer
 */
final class SQLStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/sql--execute/sql.yaml');

        $data = [
            new SQLExecuteStep([
                new Query(
                    'mysql',
                    'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                ),
                new Query(
                    'sqlite',
                    'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'
                ),
            ]),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/sql--execute/sql.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(SQLExecuteStep::class, $deserialized);

            $expectedStep = new SQLExecuteStep([
                new Query(
                    'mysql',
                    'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                ),
                new Query(
                    'sqlite',
                    'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'
                ),
            ]);

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

class_alias(SQLStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\SQLStepNormalizerTest');
