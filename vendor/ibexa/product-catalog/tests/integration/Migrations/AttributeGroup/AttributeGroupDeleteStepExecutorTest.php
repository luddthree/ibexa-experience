<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

final class AttributeGroupDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupDeleteStepExecutor $executor;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->attributeGroupService = self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
        $this->executor = new AttributeGroupDeleteStepExecutor($this->attributeGroupService);
        $this->configureExecutor($this->executor);
        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideForCriteriaExceptions
     */
    public function testCriteriaExceptions(AttributeGroupDeleteStep $step, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        $this->executor->handle($step);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep,
     *     string,
     * }>
     */
    public function provideForCriteriaExceptions(): iterable
    {
        yield [
            new AttributeGroupDeleteStep(new LogicalAnd(
                new FieldValueCriterion('foo_field', 'VALUE', '='),
            )),
            'Only Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion is supported.',
        ];

        yield [
            new AttributeGroupDeleteStep(
                new FieldValueCriterion('foo_field', 'VALUE', '='),
            ),
            'Only identifier field is supported.',
        ];

        yield [
            new AttributeGroupDeleteStep(
                new FieldValueCriterion('identifier', ['VALUE'], '='),
            ),
            'Only string value for identifier field is supported.',
        ];

        yield [
            new AttributeGroupDeleteStep(
                new FieldValueCriterion('identifier', 'VALUE', 'IN'),
            ),
            'Only "=" comparison operator is supported.',
        ];
    }

    public function testExecution(): void
    {
        $originalGroup = $this->attributeGroupService->getAttributeGroup('empty');
        self::assertSame('empty', $originalGroup->getIdentifier());
        self::assertSame('Empty', $originalGroup->getName());
        self::assertSame(1, $originalGroup->getPosition());

        $step = new AttributeGroupDeleteStep(
            new FieldValueCriterion('identifier', 'empty', '='),
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $this->expectException(NotFoundException::class);
        $this->attributeGroupService->getAttributeGroup('empty');
    }
}
