<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifier;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifierValidator;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCustomerGroupIdentifierValidator
 */
final class UniqueCustomerGroupIdentifierValidatorTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CustomerGroupServiceInterface $customerGroupService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueCustomerGroupIdentifierValidator $validator;

    protected function setUp(): void
    {
        $this->customerGroupService = $this->createMock(CustomerGroupServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueCustomerGroupIdentifierValidator($this->customerGroupService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     */
    public function testSkipValidation(object $value): void
    {
        $this->customerGroupService
            ->expects(self::never())
            ->method('getCustomerGroupByIdentifier');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueCustomerGroupIdentifier());
    }

    /**
     * @return iterable<string,array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing code (CustomerGroupCreateData)' => [
            new CustomerGroupCreateData(),
        ];

        yield 'missing code (CustomerGroupUpdateData)' => [
            new CustomerGroupUpdateData(1, $this->getTestLanguage()),
        ];
    }

    public function testValidCustomerGroupCreateData(): void
    {
        $identifier = 'foo';

        $this->customerGroupService
            ->method('getCustomerGroupByIdentifier')
            ->with($identifier)
            ->willReturn(null);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new CustomerGroupCreateData();
        $value->setIdentifier($identifier);

        $this->validator->validate($value, new UniqueCustomerGroupIdentifier());
    }

    public function testValidCustomerGroupUpdateData(): void
    {
        $updatedCustomerGroupId = 42;
        $identifier = 'foo';

        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')->willReturn($updatedCustomerGroupId);

        $this->customerGroupService
            ->method('getCustomerGroupByIdentifier')
            ->with($identifier)
            ->willReturn($customerGroup);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new CustomerGroupUpdateData($updatedCustomerGroupId, $this->getTestLanguage());
        $value->setIdentifier($identifier);

        $this->validator->validate($value, new UniqueCustomerGroupIdentifier());
    }

    public function testInvalidCustomerGroupCreateData(): void
    {
        $identifier = 'foo';

        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')->willReturn(43);

        $this->customerGroupService
            ->method('getCustomerGroupByIdentifier')
            ->with($identifier)
            ->willReturn($customerGroup);

        $constraint = new UniqueCustomerGroupIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock($identifier));

        $value = new CustomerGroupCreateData();
        $value->setIdentifier($identifier);

        $this->validator->validate($value, $constraint);
    }

    public function testInvalidCustomerGroupUpdateData(): void
    {
        $updatedCustomerGroupId = 42;
        $conflictingCustomerGroupId = 43;
        $identifier = 'foo';

        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->method('getId')->willReturn($conflictingCustomerGroupId);

        $this->customerGroupService
            ->method('getCustomerGroupByIdentifier')
            ->with($identifier)
            ->willReturn($customerGroup);

        $constraint = new UniqueCustomerGroupIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock($identifier));

        $value = new CustomerGroupUpdateData($updatedCustomerGroupId, $this->getTestLanguage());
        $value->setIdentifier($identifier);

        $this->validator->validate($value, $constraint);
    }

    private function getConstraintViolationBuilderMock(string $identifier): ConstraintViolationBuilderInterface
    {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('atPath')
            ->with('identifier')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('%identifier%', $identifier)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        return $constraintViolationBuilder;
    }

    private function getTestLanguage(): Language
    {
        return new Language([]);
    }
}
