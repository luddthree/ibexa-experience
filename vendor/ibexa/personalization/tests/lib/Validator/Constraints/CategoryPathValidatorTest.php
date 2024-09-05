<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Validator\Constraints;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use Ibexa\Personalization\Validator\Constraints\CategoryPath;
use Ibexa\Personalization\Validator\Constraints\CategoryPathValidator;
use Ibexa\Personalization\Validator\Constraints\FulfilledScenarioStrategyLevel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @covers \Ibexa\Personalization\Validator\Constraints\CategoryPathValidator
 */
final class CategoryPathValidatorTest extends TestCase
{
    private CategoryPath $categoryPathConstraint;

    private CategoryPathValidator $categoryPathValidator;

    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    /** @var \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConstraintViolationBuilderInterface $constraintViolationBuilder;

    public function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->categoryPathConstraint = new CategoryPath();
        $this->categoryPathValidator = new CategoryPathValidator();
        $this->categoryPathValidator->initialize($this->executionContext);
        $this->constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
    }

    public function testThrowExceptionWhenUnexpectedConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Expected argument of type "%s", "%s" given',
                CategoryPath::class,
                FulfilledScenarioStrategyLevel::class
            )
        );

        $this->categoryPathValidator->validate('test', new FulfilledScenarioStrategyLevel());
    }

    public function testThrowExceptionWhenUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Expected argument of type "%s", "%s" given',
                ScenarioExclusionsData::class,
                'array'
            )
        );

        $this->categoryPathValidator->validate(['test'], $this->categoryPathConstraint);
    }

    /**
     * @dataProvider provideDataForTestValidate
     */
    public function testValidate(string $path): void
    {
        $this->executionContext
            ->expects(self::never())
            ->method('addViolation');

        $this->categoryPathValidator->validate($this->createScenarioExclusionsData($path), $this->categoryPathConstraint);
    }

    /**
     * @dataProvider provideDataForTestValidateWithViolation
     */
    public function testValidateWithViolation(string $path): void
    {
        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->willReturn($this->constraintViolationBuilder);

        $this->constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->willReturn($this->constraintViolationBuilder);

        $this->constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation')
            ->willReturn($this->constraintViolationBuilder);

        $this->categoryPathValidator->validate($this->createScenarioExclusionsData($path), $this->categoryPathConstraint);
    }

    /**
     * @return iterable<array{string}>
     */
    public function provideDataForTestValidate(): iterable
    {
        yield ['/1/2'];
        yield ['/1/2/3'];
        yield ['/1/2/3/4/5'];
        yield ['/1/2/4/10/20/100/100'];
        yield ['/10/100/1000/1000000'];
    }

    /**
     * @return iterable<array{string}>
     */
    public function provideDataForTestValidateWithViolation(): iterable
    {
        yield ['/1/2/3/'];
        yield ['foo1/2/3/'];
        yield ['/1/2/3bar'];
        yield ['/1/2/3/test'];
        yield ['test'];
        yield ['/1'];
        yield ['/1/'];
        yield ['/f1/2/3/'];
        yield ['foo/1/2/3/bar'];
        yield ['foo//3/bar'];
        yield ['///'];
        yield ['//3/'];
    }

    private function createScenarioExclusionsData(string $path): ScenarioExclusionsData
    {
        return new ScenarioExclusionsData(
            true,
            new ScenarioExcludedCategoriesData(true, [$path])
        );
    }
}
