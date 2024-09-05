<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValue;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValueValidator;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AttributeValueValidatorTest extends TestCase
{
    private const EXAMPLE_TYPE_IDENTIFIER = 'integer';
    private const EXAMPLE_VALUE = 'example';

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ValueValidatorRegistryInterface $registry;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private AttributeValueValidator $validator;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ValueValidatorRegistryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new AttributeValueValidator($this->registry);
        $this->validator->initialize($this->executionContext);
    }

    public function testSkipValidationForNullValue(): void
    {
        $this->registry
            ->expects(self::never())
            ->method('hasValidator');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate(null, new AttributeValue());
    }

    public function testSkipValidationForMissingValidator(): void
    {
        $this->registry
            ->expects(self::once())
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(false);

        $this->registry
            ->expects(self::never())
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER);

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate(
            self::EXAMPLE_VALUE,
            new AttributeValue([
                'definition' => $this->createDefinitionMock(self::EXAMPLE_TYPE_IDENTIFIER),
            ])
        );
    }

    public function testValidate(): void
    {
        $errors = [
            new ValueValidationError('[foo]', 'Invalid value foo', ['%param%' => 'value']),
            new ValueValidationError(null, 'Invalid value in general'),
        ];

        $definition = $this->createDefinitionMock(self::EXAMPLE_TYPE_IDENTIFIER);

        $valueValidator = $this->createMock(ValueValidatorInterface::class);
        $valueValidator
            ->expects(self::once())
            ->method('validateValue')
            ->with($definition, self::EXAMPLE_VALUE)
            ->willReturn($errors);

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(true);

        $this->registry
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn($valueValidator);

        $this->executionContext
            ->expects(self::exactly(2))
            ->method('buildViolation')
            ->withConsecutive(
                ['Invalid value foo'],
                ['Invalid value in general']
            )
            ->willReturnOnConsecutiveCalls(
                $this->getConstraintViolationBuilderMock('[foo]', ['%param%' => 'value']),
                $this->getConstraintViolationBuilderMock(null)
            );

        $this->validator->validate(
            self::EXAMPLE_VALUE,
            new AttributeValue([
                'definition' => $this->createDefinitionMock(self::EXAMPLE_TYPE_IDENTIFIER),
            ])
        );
    }

    private function createDefinitionMock(string $typeIdentifier): AttributeDefinitionInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($typeIdentifier);

        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getType')->willReturn($type);

        return $definition;
    }

    /**
     * @param array<string,mixed> $params
     */
    private function getConstraintViolationBuilderMock(?string $path, array $params = []): ConstraintViolationBuilderInterface
    {
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('atPath')
            ->with($path)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameters')
            ->with($params)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        return $constraintViolationBuilder;
    }
}
