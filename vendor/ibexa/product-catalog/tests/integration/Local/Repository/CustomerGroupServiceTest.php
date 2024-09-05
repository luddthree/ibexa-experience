<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupIdentifier;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupName;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\StorageSchema;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\CustomerGroupService
 *
 * @group customer-group-service
 */
final class CustomerGroupServiceTest extends BaseCustomerGroupServiceTest
{
    public function testCreate(): CustomerGroupInterface
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupCreateStruct();

        $customerGroup = $service->createCustomerGroup($struct);

        self::assertSame('foo', $customerGroup->getIdentifier());
        self::assertSame('Foo', $customerGroup->getName());
        self::assertSame('Lorem Ipsum', $customerGroup->getDescription());

        return $customerGroup;
    }

    public function testCreateIdentifierValidation(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupCreateStruct(CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'struct\' is invalid: Customer Group with identifier "answer to everything" already exists');
        $service->createCustomerGroup($struct);
    }

    /**
     * @dataProvider provideForFindCustomerGroups
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testFindCustomerGroups(?CustomerGroupQuery $query, int $expectedCount): void
    {
        $service = self::getCustomerGroupService();

        $list = $service->findCustomerGroups($query);

        self::assertSame($expectedCount, $list->getTotalCount());
        self::assertContainsOnlyInstancesOf(CustomerGroupInterface::class, $list);
    }

    /**
     * @return iterable<string, array{0: \Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery|null, 1: int}>
     */
    public function provideForFindCustomerGroups(): iterable
    {
        yield 'NULL query' => [
            null,
            5,
        ];
        yield 'non existent identifier' => [
            new CustomerGroupQuery(new CustomerGroupIdentifier('non-existent')),
            0,
        ];

        yield sprintf('"%s" identifier', CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER) => [
            new CustomerGroupQuery(new CustomerGroupIdentifier(CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER)),
            1,
        ];

        yield 'Customer Group name search equals "Answer To Life, Universe and Everything"' => [
            new CustomerGroupQuery(
                new CustomerGroupName('Answer To Life, Universe and Everything'),
            ),
            1,
        ];

        yield 'Customer Group name search contains "Life, Universe"' => [
            new CustomerGroupQuery(
                new CustomerGroupName('Life, Universe', FieldValueCriterion::COMPARISON_CONTAINS),
            ),
            1,
        ];

        yield 'Customer Group name search starts with "Answer"' => [
            new CustomerGroupQuery(
                new CustomerGroupName('Answer', FieldValueCriterion::COMPARISON_STARTS_WITH),
            ),
            1,
        ];

        yield 'Customer Group name search ends with "Everything"' => [
            new CustomerGroupQuery(
                new CustomerGroupName('Everything', FieldValueCriterion::COMPARISON_ENDS_WITH),
            ),
            1,
        ];

        yield 'Customer Group name search not equals "Pain"' => [
            new CustomerGroupQuery(
                new CustomerGroupName('Pain', FieldValueCriterion::COMPARISON_NEQ),
            ),
            5,
        ];

        yield sprintf('Search using custom field "%s" equals to %s', StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 66.6) => [
            new CustomerGroupQuery(
                new FieldValueCriterion(StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 66.6),
            ),
            1,
        ];

        yield sprintf('Search using custom field "%s" greater than %s', StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 0) => [
            new CustomerGroupQuery(
                new FieldValueCriterion(StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 0, FieldValueCriterion::COMPARISON_GT),
            ),
            1,
        ];

        yield sprintf('Search using custom field "%s" greater than or equal %s', StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 0) => [
            new CustomerGroupQuery(
                new FieldValueCriterion(StorageSchema::COLUMN_GLOBAL_PRICE_RATE, 0, FieldValueCriterion::COMPARISON_GTE),
            ),
            4,
        ];
    }

    public function testGetCustomerGroupByIdentifier(): void
    {
        $service = self::getCustomerGroupService();

        $customerGroup = $service->getCustomerGroupByIdentifier(CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER);

        self::assertNotNull($customerGroup);
    }

    public function testFindingNonExistentCustomerGroupByIdentifier(): void
    {
        $service = self::getCustomerGroupService();

        $customerGroup = $service->getCustomerGroupByIdentifier('foo');

        self::assertNull($customerGroup);
    }

    public function testFindCustomerGroupsWithLimitZero(): void
    {
        $service = self::getCustomerGroupService();

        $list = $service->findCustomerGroups(new CustomerGroupQuery(null, [], 0, 0));

        self::assertSame(5, $list->getTotalCount());
        self::assertSame([], $list->getCustomerGroups());
    }

    public function testGetCustomerGroup(): CustomerGroupInterface
    {
        $service = self::getCustomerGroupService();

        $customerGroup = $service->getCustomerGroup(CustomerGroupFixture::FIXTURE_ENTRY_ID);

        self::assertSame('answer to everything', $customerGroup->getIdentifier());
        self::assertSame('Answer To Life, Universe and Everything', $customerGroup->getName());
        self::assertSame('', $customerGroup->getDescription());

        return $customerGroup;
    }

    public function testGetCustomGroupThrowsNotFoundException(): void
    {
        $service = self::getCustomerGroupService();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\\Contracts\\ProductCatalog\\Values\\CustomerGroupInterface' with identifier '-1'");
        $service->getCustomerGroup(-1);
    }

    public function testUpdate(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupUpdateStruct();

        $customerGroup = $service->updateCustomerGroup($struct);

        self::assertSame(42, $customerGroup->getId());
        self::assertSame('bar', $customerGroup->getIdentifier());
        self::assertSame('Bar', $customerGroup->getName());
        self::assertSame('Luke, I\'m your father', $customerGroup->getDescription());
    }

    public function testUpdateIdentifierValidation(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupUpdateStruct(43, CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'struct\' is invalid: Customer Group with identifier "answer to everything" already exists');
        $service->updateCustomerGroup($struct);
    }

    public function testUpdateIdentifierValidationWithSameCustomerGroup(): void
    {
        $service = self::getCustomerGroupService();

        $struct = self::getCustomerGroupUpdateStruct(42, CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER);

        $customerGroup = $service->updateCustomerGroup($struct);

        self::assertSame(42, $customerGroup->getId());
        self::assertSame(CustomerGroupFixture::FIXTURE_ENTRY_IDENTIFIER, $customerGroup->getIdentifier());
        self::assertSame('Bar', $customerGroup->getName());
        self::assertSame('Luke, I\'m your father', $customerGroup->getDescription());
    }

    /**
     * @depends testGetCustomerGroup
     */
    public function testDelete(CustomerGroupInterface $customerGroup): void
    {
        $service = self::getCustomerGroupService();

        $service->deleteCustomerGroup($customerGroup);
        $id = $customerGroup->getId();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            "Could not find 'Ibexa\\Contracts\\ProductCatalog\\Values\\CustomerGroupInterface' with identifier '%d'",
            $id,
        ));
        $service->getCustomerGroup($id);
    }
}
