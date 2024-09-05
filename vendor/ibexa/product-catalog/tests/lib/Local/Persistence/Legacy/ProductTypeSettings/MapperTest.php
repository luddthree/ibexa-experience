<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Mapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Mapper
 *
 * @phpstan-type Data array{
 *     id: int,
 *     field_definition_id: int,
 *     status: int,
 *     is_virtual: bool,
 * }
 */
final class MapperTest extends TestCase
{
    private Mapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new Mapper();
    }

    /**
     * @dataProvider provideDataForTestCreateFromRow
     *
     * @phpstan-param Data $row
     */
    public function testCreateFromRow(ProductTypeSetting $expected, array $row): void
    {
        self::assertEquals(
            $expected,
            $this->mapper->createFromRow($row)
        );
    }

    /**
     * @dataProvider provideDataForTestCreateFromRows
     *
     * @param array<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting> $expected
     * @param array<Data> $rows
     */
    public function testCreateFromRows(array $expected, array $rows): void
    {
        self::assertEquals(
            $expected,
            $this->mapper->createFromRows($rows)
        );
    }

    /**
     * @return iterable<array{
     *      array<\Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting>,
     *      array<Data>
     * }>
     */
    public function provideDataForTestCreateFromRows(): iterable
    {
        yield [
            [
                new ProductTypeSetting(
                    1,
                    100,
                    true
                ),
                new ProductTypeSetting(
                    2,
                    102,
                    true
                ),
                new ProductTypeSetting(
                    3,
                    103,
                    false
                ),
            ],
            [
                [
                    'id' => 1,
                    'field_definition_id' => 100,
                    'status' => Type::STATUS_DEFINED,
                    'is_virtual' => true,
                ],
                [
                    'id' => 2,
                    'field_definition_id' => 102,
                    'status' => Type::STATUS_DEFINED,
                    'is_virtual' => true,
                ],
                [
                    'id' => 3,
                    'field_definition_id' => 103,
                    'status' => Type::STATUS_DEFINED,
                    'is_virtual' => false,
                ],
            ],
        ];
    }

    /**
     * @return iterable<array{
     *      \Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting,
     *      Data,
     * }>
     */
    public function provideDataForTestCreateFromRow(): iterable
    {
        yield 'is_virtual product type' => [
            new ProductTypeSetting(
                1,
                100,
                true
            ),
            [
                'id' => 1,
                'field_definition_id' => 100,
                'status' => Type::STATUS_DEFINED,
                'is_virtual' => true,
            ],
        ];

        yield 'Physical product type' => [
            new ProductTypeSetting(
                2,
                120,
                false
            ),
            [
                'id' => 2,
                'field_definition_id' => 120,
                'status' => Type::STATUS_DEFINED,
                'is_virtual' => false,
            ],
        ];
    }
}
