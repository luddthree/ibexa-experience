<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Validator\Constraints;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeDefinitionOptions;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeDefinitionOptionsValidator;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions as OptionsBag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AttributeDefinitionOptionsValidatorTest extends TestCase
{
    private const EXAMPLE_TYPE_IDENTIFIER = 'integer';
    private const EXAMPLE_OPTIONS = [
        'foo' => 'foo',
        'bar' => 'bar',
    ];

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\OptionsValidatorRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OptionsValidatorRegistryInterface $registry;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private AttributeDefinitionOptionsValidator $validator;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(OptionsValidatorRegistryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new AttributeDefinitionOptionsValidator($this->registry);
        $this->validator->initialize($this->executionContext);
    }

    public function testSkipValidationForUnsupportedValue(): void
    {
        $this->registry
            ->expects(self::never())
            ->method('hasValidator');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate('unsupported value', new AttributeDefinitionOptions());
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
            self::EXAMPLE_OPTIONS,
            new AttributeDefinitionOptions([
                'type' => $this->createTypeMock(self::EXAMPLE_TYPE_IDENTIFIER),
            ])
        );
    }

    public function testValidate(): void
    {
        $errors = [
            new OptionsValidatorError('[foo]', 'Invalid foo option', ['%param%' => 'value']),
            new OptionsValidatorError(null, 'Invalid bar option'),
        ];

        $optionsValidator = $this->createMock(OptionsValidatorInterface::class);
        $optionsValidator
            ->expects(self::once())
            ->method('validateOptions')
            ->with(new OptionsBag(self::EXAMPLE_OPTIONS))
            ->willReturn($errors);

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(true);

        $this->registry
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn($optionsValidator);

        $this->executionContext
            ->expects(self::exactly(2))
            ->method('buildViolation')
            ->withConsecutive(
                ['Invalid foo option'],
                ['Invalid bar option']
            )
            ->willReturnOnConsecutiveCalls(
                $this->getConstraintViolationBuilderMock('[foo]', ['%param%' => 'value']),
                $this->getConstraintViolationBuilderMock(null)
            );

        $this->validator->validate(
            self::EXAMPLE_OPTIONS,
            new AttributeDefinitionOptions([
                'type' => $this->createTypeMock(self::EXAMPLE_TYPE_IDENTIFIER),
            ])
        );
    }

    private function createTypeMock(string $identifier): AttributeTypeInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($identifier);

        return $type;
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
