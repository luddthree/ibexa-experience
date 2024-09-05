<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeGroupIdentifier;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeGroupIdentifierValidator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class UniqueAttributeGroupIdentifierValidatorTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeGroupServiceInterface $attributeGroupService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueAttributeGroupIdentifierValidator $validator;

    protected function setUp(): void
    {
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueAttributeGroupIdentifierValidator($this->attributeGroupService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     *
     * @param mixed $value
     */
    public function testSkipValidation($value): void
    {
        $this->attributeGroupService
            ->expects(self::never())
            ->method('getAttributeGroup');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueAttributeGroupIdentifier());
    }

    /**
     * @return mixed[]
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing identifier (AttributeGroupCreateData)' => [
            new AttributeGroupCreateData(),
        ];

        yield 'missing identifier (AttributeGroupUpdateData)' => [
            new AttributeGroupUpdateData(),
        ];
    }

    public function testValidAttributeGroupCreateData(): void
    {
        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with('foo')
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new AttributeGroupCreateData();
        $value->setIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeGroupIdentifier());
    }

    public function testValidAttributeGroupUpdateData(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $attributeGroup->method('getIdentifier')->willReturn('foo');

        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with('foo')
            ->willReturn($attributeGroup);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new AttributeGroupUpdateData();
        $value->setIdentifier('foo');
        $value->setOriginalIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeGroupIdentifier());
    }

    public function testInvalidAttributeGroupCreateData(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $attributeGroup->method('getIdentifier')->willReturn('foo');

        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with('foo')
            ->willReturn($attributeGroup);

        $constraint = new UniqueAttributeGroupIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock('foo'));

        $value = new AttributeGroupCreateData();
        $value->setIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeGroupIdentifier());
    }

    public function testInvalidAttributeGroupUpdateData(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $attributeGroup->method('getIdentifier')->willReturn('foo');

        $this->attributeGroupService
            ->method('getAttributeGroup')
            ->with('foo')
            ->willReturn($attributeGroup);

        $constraint = new UniqueAttributeGroupIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock('foo'));

        $value = new AttributeGroupUpdateData();
        $value->setIdentifier('foo');
        $value->setOriginalIdentifier('bar');

        $this->validator->validate($value, new UniqueAttributeGroupIdentifier());
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
}
