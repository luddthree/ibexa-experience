<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Validator\Constraints;

use Ibexa\Personalization\Validator\Constraints\FulfilledScenarioStrategyLevel;
use Ibexa\Personalization\Validator\Constraints\SupportedModelDataType;
use Ibexa\Personalization\Validator\Constraints\SupportedModelDataTypeValidator;
use Ibexa\Personalization\Value\Model\MetaData;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\ModelList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Personalization\Validator\Constraints\SupportedModelDataTypeValidator
 */
final class SupportedModelDataTypeValidatorTest extends TestCase
{
    private SupportedModelDataTypeValidator $supportedModelDataTypeValidator;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    /** @var \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConstraintViolationBuilderInterface $constraintViolationBuilder;

    public function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->supportedModelDataTypeValidator = new SupportedModelDataTypeValidator();
        $this->supportedModelDataTypeValidator->initialize($this->executionContext);
        $this->constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
    }

    public function testThrowExceptionWhenUnexpectedConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Expected argument of type "%s", "%s" given',
                SupportedModelDataType::class,
                FulfilledScenarioStrategyLevel::class
            )
        );

        $this->supportedModelDataTypeValidator->validate('test', new FulfilledScenarioStrategyLevel());
    }

    public function testThrowExceptionWhenUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "array" given');

        $this->supportedModelDataTypeValidator->validate(
            ['test'],
            $this->createSupportedModelDataType('foo', new ModelList([]))
        );
    }

    /**
     * @dataProvider provideDataForTestValidate
     */
    public function testValidate(
        string $referenceCode,
        string $dataType
    ): void {
        $this->executionContext
            ->expects(self::never())
            ->method('addViolation');

        $this->supportedModelDataTypeValidator->validate(
            $dataType,
            $this->createSupportedModelDataType($referenceCode, $this->getModelList())
        );
    }

    /**
     * @dataProvider provideDataForTestValidateWithViolation
     */
    public function testValidateWithViolation(
        string $referenceCode,
        string $dataTypeViolation
    ): void {
        $this->executionContext
            ->expects(self::atLeastOnce())
            ->method('buildViolation')
            ->with(
                'Data type {{ data_type }} is not supported for model {{ model }}',
            )
            ->willReturn($this->constraintViolationBuilder);

        $this->constraintViolationBuilder
            ->expects(self::atLeastOnce())
            ->method('setParameters')
            ->with(
                [
                    '{{ data_type }}' => $dataTypeViolation,
                    '{{ model }}' => $referenceCode,
                ]
            )
            ->willReturn($this->constraintViolationBuilder);

        $this->constraintViolationBuilder
            ->expects(self::atLeastOnce())
            ->method('addViolation');

        $this->supportedModelDataTypeValidator->validate(
            $dataTypeViolation,
            $this->createSupportedModelDataType($referenceCode, $this->getModelList())
        );
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideDataForTestValidate(): iterable
    {
        yield ['foo', 'submodels'];
        yield ['bar', 'submodels'];
        yield ['baz', 'default'];
    }

    /**
     * @return iterable<array{string, string}>
     */
    public function provideDataForTestValidateWithViolation(): iterable
    {
        yield ['bar', 'segments'];
        yield ['baz', 'submodels'];
        yield ['baz', 'segments'];
    }

    private function createSupportedModelDataType(
        string $referenceCode,
        ModelList $modelList
    ): SupportedModelDataType {
        return new SupportedModelDataType(
            [
                'referenceCode' => $referenceCode,
                'modelList' => $modelList,
            ]
        );
    }

    private function getModelList(): ModelList
    {
        return new ModelList(
            [
                $this->createTestModel('foo', true, true),
                $this->createTestModel('bar', true, false),
                $this->createTestModel('baz', false, false),
            ]
        );
    }

    private function createTestModel(
        string $referenceCode,
        bool $submodelsSupported,
        bool $segmentsSupported
    ): Model {
        return new Model(
            $referenceCode . '_type',
            $referenceCode,
            [],
            [],
            true,
            true,
            $submodelsSupported,
            $segmentsSupported,
            true,
            true,
            true,
            new MetaData(null, null, null, null),
            [],
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
        );
    }
}
