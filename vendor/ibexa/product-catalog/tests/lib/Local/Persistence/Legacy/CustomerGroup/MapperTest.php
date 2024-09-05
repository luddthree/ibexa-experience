<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Mapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Mapper
 */
final class MapperTest extends TestCase
{
    public function testCreateFromRow(): void
    {
        $expectedCustomerGroup = $this->createExpectedCustomerGroup();

        $mapper = new Mapper();

        $row = [
            'id' => 1,
            'identifier' => 'foo',
            'global_price_rate' => '42',
        ];

        $translations = [
            [
                'id' => 777,
                'customer_group_id' => 1,
                'language_id' => 2,
                'name' => 'Foo',
                'description' => 'Lorem Ipsum',
            ],
        ];

        $customerGroup = $mapper->createFromRow($row, $translations);

        self::assertEquals($expectedCustomerGroup, $customerGroup);
    }

    public function testCreateFromRows(): void
    {
        $mapper = new Mapper();

        $rows = [
            [
                'id' => 1,
                'identifier' => 'foo',
                'name' => 'Foo',
                'description' => 'Lorem Ipsum',
                'global_price_rate' => '42',
            ],
        ];

        $translations = [
            [
                'id' => 777,
                'customer_group_id' => 1,
                'language_id' => 2,
                'name' => 'Foo',
                'description' => 'Lorem Ipsum',
            ],
        ];

        $customerGroups = $mapper->createFromRows($rows, $translations);

        self::assertNotEmpty($customerGroups);
        self::assertContainsOnlyInstancesOf(CustomerGroup::class, $customerGroups);

        $expectedCustomerGroups = [$this->createExpectedCustomerGroup()];
        self::assertEquals($expectedCustomerGroups, $customerGroups);
    }

    private function createExpectedCustomerGroup(): CustomerGroup
    {
        $customerGroup = new CustomerGroup([
            'id' => 1,
            'identifier' => 'foo',
            'globalPriceRate' => '42',
        ]);

        $customerGroup->setName(2, 'Foo');
        $customerGroup->setDescription(2, 'Lorem Ipsum');

        return $customerGroup;
    }
}
