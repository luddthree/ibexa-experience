<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\FieldType\CustomerGroup\ViewParameterProvider;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use PHPUnit\Framework\TestCase;

final class ViewParameterProviderTest extends TestCase
{
    private const EXAMPLE_ID = 572;
    private const EXAMPLE_IDENTIFIER = 'standard';
    private const EXAMPLE_NAME = 'Standard';
    private const EXAMPLE_DESCRIPTION = 'Default customer group';

    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $customerGroupService;

    private ViewParameterProvider $viewParameterProvider;

    protected function setUp(): void
    {
        $this->customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);
        $this->viewParameterProvider = new ViewParameterProvider($this->customerGroupService);
    }

    public function testGetViewParametersForNonEmptyValue(): void
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')->willReturn(self::EXAMPLE_ID);
        $customerGroup->method('getIdentifier')->willReturn(self::EXAMPLE_IDENTIFIER);
        $customerGroup->method('getName')->willReturn(self::EXAMPLE_NAME);
        $customerGroup->method('getDescription')->willReturn(self::EXAMPLE_DESCRIPTION);

        $this->customerGroupService
            ->expects(self::once())
            ->method('getCustomerGroup')
            ->with(self::EXAMPLE_ID)
            ->willReturn($customerGroup);

        $value = new Value(self::EXAMPLE_ID);

        $field = $this->createMock(Field::class);
        $field->method('__get')->with('value')->willReturn($value);

        self::assertSame(
            [
                'customer_group' => $customerGroup,
                'identifier' => self::EXAMPLE_IDENTIFIER,
                'name' => self::EXAMPLE_NAME,
                'description' => self::EXAMPLE_DESCRIPTION,
            ],
            $this->viewParameterProvider->getViewParameters($field)
        );
    }

    public function testGetViewParametersForEmptyCustomerGroupId(): void
    {
        $value = new Value(null);

        $field = $this->createMock(Field::class);
        $field->method('__get')->with('value')->willReturn($value);

        self::assertSame(
            [
                'customer_group' => null,
            ],
            $this->viewParameterProvider->getViewParameters($field)
        );
    }
}
