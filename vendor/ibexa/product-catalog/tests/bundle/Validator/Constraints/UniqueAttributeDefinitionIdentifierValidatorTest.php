<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionUpdateData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeDefinitionIdentifier;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeDefinitionIdentifierValidator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueAttributeDefinitionIdentifierValidator
 */
final class UniqueAttributeDefinitionIdentifierValidatorTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private UniqueAttributeDefinitionIdentifierValidator $validator;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new UniqueAttributeDefinitionIdentifierValidator($this->attributeDefinitionService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     *
     * @param mixed $value
     */
    public function testSkipValidation($value): void
    {
        $this->attributeDefinitionService
            ->expects(self::never())
            ->method('getAttributeDefinition');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UniqueAttributeDefinitionIdentifier());
    }

    /**
     * @return iterable<string, array{object}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'unsupported data class' => [
            new stdClass(),
        ];

        yield 'missing identifier (AttributeDefinitionCreateData)' => [
            new AttributeDefinitionCreateData(),
        ];

        yield 'missing identifier (AttributeDefinitionUpdateData)' => [
            new AttributeDefinitionUpdateData(),
        ];
    }

    public function testValidAttributeGroupCreateData(): void
    {
        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with('foo')
            ->willThrowException($this->createMock(NotFoundException::class));

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new AttributeDefinitionCreateData();
        $value->setIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeDefinitionIdentifier());
    }

    public function testValidAttributeDefinitionUpdateData(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn('foo');

        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with('foo')
            ->willReturn($attributeDefinition);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $value = new AttributeDefinitionUpdateData();
        $value->setIdentifier('foo');
        $value->setOriginalIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeDefinitionIdentifier());
    }

    public function testInvalidAttributeDefinitionCreateData(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn('foo');

        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with('foo')
            ->willReturn($attributeDefinition);

        $constraint = new UniqueAttributeDefinitionIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock('foo'));

        $value = new AttributeDefinitionCreateData();
        $value->setIdentifier('foo');

        $this->validator->validate($value, new UniqueAttributeDefinitionIdentifier());
    }

    public function testInvalidAttributeDefinitionUpdateData(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn('foo');

        $this->attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with('foo')
            ->willReturn($attributeDefinition);

        $constraint = new UniqueAttributeDefinitionIdentifier();

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->getConstraintViolationBuilderMock('foo'));

        $value = new AttributeDefinitionUpdateData();
        $value->setIdentifier('foo');
        $value->setOriginalIdentifier('bar');

        $this->validator->validate($value, new UniqueAttributeDefinitionIdentifier());
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

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        return $constraintViolationBuilder;
    }
}
