<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\Gateway;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\ObjectType;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Exception\MappingExceptionInterface;
use Ibexa\Contracts\CorePersistence\Exception\RuntimeMappingExceptionInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Gateway\DoctrineSchemaMetadata;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\Gateway\DoctrineSchemaMetadata
 */
final class DoctrineSchemaMetadataTest extends TestCase
{
    private const COLUMN_ID = 'id';

    /**
     * @phpstan-param array<string, \Doctrine\DBAL\Types\Types::*>|null $columnTypesMap
     *
     * @param array<string>|null $identifierColumns
     */
    private function getDoctrineSchemaMetadata(
        ?Connection $connection = null,
        ?array $columnTypesMap = null,
        ?array $identifierColumns = null
    ): DoctrineSchemaMetadata {
        $connection ??= $this->getConnection();
        $columnTypesMap ??= self::getDefaultColumnToDoctrineTypesMap();
        $identifierColumns ??= [self::COLUMN_ID];

        return new DoctrineSchemaMetadata(
            $connection,
            stdClass::class,
            'foo',
            $columnTypesMap,
            $identifierColumns,
        );
    }

    private function getConnection(): Connection
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')
            ->willReturn(new MySQL80Platform());

        return $connection;
    }

    /**
     * @return array<string, \Doctrine\DBAL\Types\Types::*>
     */
    private static function getDefaultColumnToDoctrineTypesMap(): array
    {
        return [
            self::COLUMN_ID => Types::INTEGER,
            'int' => Types::INTEGER,
            'bigint' => Types::BIGINT,
            'date' => Types::DATE_MUTABLE,
            'date_immutable' => Types::DATE_IMMUTABLE,
            'datetime' => Types::DATETIME_MUTABLE,
            'object' => Types::OBJECT,
            'json' => Types::JSON,
            'bool' => Types::BOOLEAN,
        ];
    }

    public function testThrowsConversionFailureIfTypeMismatch(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Could not convert database value "foo" to Doctrine Type date. Expected format: Y-m-d');
        $this->getDoctrineSchemaMetadata()->convertToPHPValues([
            'date' => 'foo',
        ]);
    }

    public function testThrowsConversionToDatabaseValuesFailureIfTypeMismatch(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("Could not convert PHP value 'foo' of type 'string' to type 'date'. Expected one of the following types: null, DateTime");
        $this->getDoctrineSchemaMetadata()->convertToDatabaseValues([
            'date' => 'foo',
        ]);
    }

    public function testThrowsInvalidArgumentExceptionWhenColumnTypeNotFound(): void
    {
        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Column "foo" does not exist in "foo" table. Available columns: "id", "int", '
            . '"bigint", "date", "date_immutable", "datetime", "object", "json", "bool"'
        );
        $this->getDoctrineSchemaMetadata()->getColumnType('foo');
    }

    public function testThrowsInvalidArgumentExceptionWhenConvertingToDatabaseValuesWithMissingColumns(): void
    {
        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Column "foo" does not exist in "foo" table. Available columns: "id", "int", '
            . '"bigint", "date", "date_immutable", "datetime", "object", "json", "bool"'
        );
        $this->getDoctrineSchemaMetadata()->convertToDatabaseValues(['foo' => '']);
    }

    public function testThrowsInvalidArgumentExceptionWhenConvertingToPHPValuesWithMissingColumns(): void
    {
        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Column "foo" does not exist in "foo" table. Available columns: "id", "int", '
            . '"bigint", "date", "date_immutable", "datetime", "object", "json", "bool"'
        );
        $this->getDoctrineSchemaMetadata()->convertToPHPValues(['foo' => '']);
    }

    public function testThrowsInvalidArgumentExceptionWhenBindingTypesWithMissingColumns(): void
    {
        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Column "foo" does not exist in "foo" table. Available columns: "id", "int", '
            . '"bigint", "date", "date_immutable", "datetime", "object", "json", "bool"'
        );
        $this->getDoctrineSchemaMetadata()->getBindingTypesForData(['foo' => '']);
    }

    public function testConvertToDatabaseValues(): void
    {
        $date = new DateTime('2021-01-01 13:30:00');
        $dateImmutable = DateTimeImmutable::createFromMutable($date);
        $dateTime = clone $date;
        $object = new stdClass();
        $object->foo = 'bar';

        $phpValues = [
            self::COLUMN_ID => '1',
            'int' => 1,
            'bigint' => 1,
            'date' => $date,
            'date_immutable' => $dateImmutable,
            'datetime' => $dateTime,
            'json' => ['some_json' => 'some_value'],
            'object' => $object,
            'bool' => true,
        ];
        $result = $this->getDoctrineSchemaMetadata()->convertToDatabaseValues($phpValues);

        self::assertSame([
            self::COLUMN_ID => '1',
            'int' => 1,
            'bigint' => 1,
            'date' => '2021-01-01',
            'date_immutable' => '2021-01-01',
            'datetime' => '2021-01-01 13:30:00',
            'json' => '{"some_json":"some_value"}',
            'object' => 'O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}',
            'bool' => 1,
        ], $result);
    }

    public function testConvertToPHPValues(): void
    {
        $dateTime = new DateTime('2021-01-01 13:30:00');
        $dateImmutable = DateTimeImmutable::createFromMutable($dateTime);
        $dateImmutable = $dateImmutable->setTime(0, 0);
        $date = clone $dateTime;
        $date->setTime(0, 0);
        $object = new stdClass();
        $object->foo = 'bar';

        $databaseValues = [
            self::COLUMN_ID => '1',
            'int' => '1',
            'bigint' => (string) 1,
            'date' => '2021-01-01',
            'date_immutable' => '2021-01-01',
            'datetime' => '2021-01-01 13:30:00',
            'json' => '{"some_json":"some_value"}',
            'object' => 'O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}',
            'bool' => '1',
        ];

        $result = $this->getDoctrineSchemaMetadata()->convertToPHPValues($databaseValues);

        self::assertSame(1, $result[self::COLUMN_ID]);

        self::assertSame(1, $result['int']);

        self::assertSame((string) 1, $result['bigint']);

        self::assertInstanceOf(DateTime::class, $result['date']);
        self::assertEquals($date, $result['date']);

        self::assertInstanceOf(DateTimeImmutable::class, $result['date_immutable']);
        self::assertEquals($dateImmutable, $result['date_immutable']);

        self::assertInstanceOf(DateTime::class, $result['datetime']);
        self::assertEquals($dateTime, $result['datetime']);

        self::assertSame(['some_json' => 'some_value'], $result['json']);

        self::assertInstanceOf(stdClass::class, $result['object']);
        self::assertEquals($object, $result['object']);

        self::assertIsBool($result['bool']);
        self::assertTrue($result['bool']);
    }

    public function testGetBindingTypesForData(): void
    {
        $date = new DateTime('2021-01-01 13:30:00');
        $object = new stdClass();
        $object->foo = 'bar';
        $data = [
            self::COLUMN_ID => '1',
            'int' => 1,
            'bigint' => 1,
            'date' => $date,
            'json' => ['some_json' => 'some_value'],
            'object' => $object,
            'bool' => true,
        ];

        $bindingTypes = $this->getDoctrineSchemaMetadata()->getBindingTypesForData($data);
        self::assertSame([
            self::COLUMN_ID => ParameterType::INTEGER,
            'int' => ParameterType::INTEGER,
            'bigint' => ParameterType::STRING,
            'date' => ParameterType::STRING,
            'json' => ParameterType::STRING,
            'object' => ParameterType::STRING,
            'bool' => ParameterType::BOOLEAN,
        ], $bindingTypes);
    }

    public function testGetColumnType(): void
    {
        $types = [
            self::COLUMN_ID => IntegerType::class,
            'int' => IntegerType::class,
            'bigint' => BigIntType::class,
            'date' => DateType::class,
            'date_immutable' => DateImmutableType::class,
            'datetime' => DateTimeType::class,
            'object' => ObjectType::class,
            'json' => JsonType::class,
            'bool' => BooleanType::class,
        ];

        $metadata = $this->getDoctrineSchemaMetadata();
        foreach ($types as $columnName => $typeClass) {
            self::assertInstanceOf($typeClass, $metadata->getColumnType($columnName));
        }
    }

    public function testGetColumns(): void
    {
        $columns = $this->getDoctrineSchemaMetadata()->getColumns();

        self::assertSame([
            self::COLUMN_ID,
            'int',
            'bigint',
            'date',
            'date_immutable',
            'datetime',
            'object',
            'json',
            'bool',
        ], $columns);
    }

    public function testGetSingleColumnIdentifier(): void
    {
        $metadata = $this->getDoctrineSchemaMetadata();
        self::assertSame(self::COLUMN_ID, $metadata->getIdentifierColumn());
    }

    public function testInheritanceMetadataWithSameDiscriminatorThrows(): void
    {
        $metadata = $this->getDoctrineSchemaMetadata();
        $fooSubMetadata = $this->getDoctrineSchemaMetadata();
        $metadata->addSubclass('foo', $fooSubMetadata);
        self::assertSame($metadata, $fooSubMetadata->getParentMetadata());
        $barSubMetadata = $this->getDoctrineSchemaMetadata();
        $metadata->addSubclass('bar', $barSubMetadata);
        self::assertSame($metadata, $barSubMetadata->getParentMetadata());

        $this->expectException(MappingExceptionInterface::class);
        $this->expectExceptionMessage('"foo" is already added as a discriminator for a subtype.');
        $metadata->addSubclass('foo', $this->getDoctrineSchemaMetadata());
    }

    public function testGetSingleColumnIdentifierWithCompositePrimaryKeyThrowsException(): void
    {
        $idColumns = [self::COLUMN_ID, 'second_id'];
        $metadata = $this->getDoctrineSchemaMetadata(null, null, $idColumns);

        $this->expectException(MappingExceptionInterface::class);
        $this->expectExceptionMessage('Attempted to get single ID column on composite primary key schema.');
        $metadata->getIdentifierColumn();
    }

    public function testGetSingleColumnIdentifierWithNoPrimaryKeyThrowsException(): void
    {
        $idColumns = [];
        $metadata = $this->getDoctrineSchemaMetadata(null, null, $idColumns);

        $this->expectException(MappingExceptionInterface::class);
        $this->expectExceptionMessage('No ID column defined for schema');
        $metadata->getIdentifierColumn();
    }

    public function testThrowsWhenTranslationMetadataRequestButNoneSet(): void
    {
        $metadata = $this->getDoctrineSchemaMetadata();

        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata '
            . 'does not contain translation metadata. Ensure that '
            . 'Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata::setTranslationSchemaMetadata'
            . ' has been called.'
        );
        $metadata->getTranslationSchemaMetadata();
    }
}
