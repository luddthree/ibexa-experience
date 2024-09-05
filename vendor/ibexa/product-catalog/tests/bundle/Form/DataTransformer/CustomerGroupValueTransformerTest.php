<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupValueTransformer;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CustomerGroupValueTransformer
 */
final class CustomerGroupValueTransformerTest extends TestCase
{
    private const MOCKED_EXISTING_CUSTOMER_GROUP_ID = 1;
    private const MOCKED_NON_EXISTING_CUSTOMER_GROUP_ID = 2;

    private CustomerGroupValueTransformer $transformer;

    protected function setUp(): void
    {
        $customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);

        $customerGroupService
            ->method('getCustomerGroup')
            ->willReturnCallback(function (int $id) {
                if ($id === self::MOCKED_EXISTING_CUSTOMER_GROUP_ID) {
                    return $this->createMock(CustomerGroupInterface::class);
                }

                throw new NotFoundException('', '$id');
            });

        $this->transformer = new CustomerGroupValueTransformer($customerGroupService);
    }

    public function testTransform(): void
    {
        self::assertNull($this->transformer->transform(null));
        self::assertNull($this->transformer->transform(new Value(null)));
        self::assertNotNull($this->transformer->transform(new Value(self::MOCKED_EXISTING_CUSTOMER_GROUP_ID)));
    }

    public function testThrowsTransformationFailedWhenCustomerGroupNotExists(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('CustomerGroup with ID 2 not found');
        $this->transformer->transform(new Value(self::MOCKED_NON_EXISTING_CUSTOMER_GROUP_ID));
    }

    public function testThrowsTransformationFailedWhenNotCustomerGroupPassed(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a Ibexa\ProductCatalog\FieldType\CustomerGroup\Value instance');
        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        self::assertNull($this->transformer->reverseTransform(null));

        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')
            ->willReturn(self::MOCKED_EXISTING_CUSTOMER_GROUP_ID);

        $value = $this->transformer->reverseTransform($customerGroup);
        self::assertNotNull($value);
        self::assertSame(self::MOCKED_EXISTING_CUSTOMER_GROUP_ID, $value->getId());
    }

    public function testReverseTransformThrowsTransformationFailedWhenNotValuePassed(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface instance');
        $this->transformer->reverseTransform(new stdClass());
    }
}
