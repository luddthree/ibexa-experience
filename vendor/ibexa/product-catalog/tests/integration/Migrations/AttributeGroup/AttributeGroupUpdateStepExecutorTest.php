<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

final class AttributeGroupUpdateStepExecutorTest extends AbstractStepExecutorTest
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupUpdateStepExecutor $executor;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->attributeGroupService = self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
        $this->executor = new AttributeGroupUpdateStepExecutor($this->attributeGroupService);
        $this->configureExecutor($this->executor);
        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideForCriteriaExceptions
     */
    public function testCriteriaExceptions(AttributeGroupUpdateStep $step, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->executor->handle($step);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep,
     *     string,
     * }>
     */
    public function provideForCriteriaExceptions(): iterable
    {
        yield [
            $this->createAttributeGroupUpdateStep(new LogicalAnd(
                new FieldValueCriterion('foo_field', 'VALUE', '='),
            )),
            'Only Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion is supported.',
        ];

        yield [
            $this->createAttributeGroupUpdateStep(
                new FieldValueCriterion('foo_field', 'VALUE', '='),
            ),
            'Only identifier field is supported.',
        ];

        yield [
            $this->createAttributeGroupUpdateStep(
                new FieldValueCriterion('identifier', ['VALUE'], '='),
            ),
            'Only string value for identifier field is supported.',
        ];

        yield [
            $this->createAttributeGroupUpdateStep(
                new FieldValueCriterion('identifier', 'VALUE', 'IN'),
            ),
            'Only "=" comparison operator is supported.',
        ];
    }

    public function testExecution(): void
    {
        $originalGroup = $this->attributeGroupService->getAttributeGroup('dimensions');
        self::assertSame('dimensions', $originalGroup->getIdentifier());
        self::assertSame('Dimensions', $originalGroup->getName());
        self::assertSame(0, $originalGroup->getPosition());

        $step = new AttributeGroupUpdateStep(
            new FieldValueCriterion('identifier', 'dimensions', '='),
            'foo_dimensions',
            null,
            null,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $group = $this->attributeGroupService->getAttributeGroup('foo_dimensions');
        self::assertSame('foo_dimensions', $group->getIdentifier());
        self::assertSame('Dimensions', $group->getName());
        self::assertSame(0, $group->getPosition());

        $step = new AttributeGroupUpdateStep(
            new FieldValueCriterion('identifier', 'foo_dimensions', '='),
            'dimensions',
            [
                'eng-US' => 'Is it really another Dimension?',
            ],
            42,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $group = $this->attributeGroupService->getAttributeGroup('dimensions');
        self::assertSame('dimensions', $group->getIdentifier());
        self::assertSame('Is it really another Dimension?', $group->getName());
        self::assertSame(42, $group->getPosition());
    }

    private function createAttributeGroupUpdateStep(CriterionInterface $criterion): AttributeGroupUpdateStep
    {
        return new AttributeGroupUpdateStep(
            $criterion,
            null,
            null,
            null,
        );
    }
}
