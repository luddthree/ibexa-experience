<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStepExecutor
 */
final class AttributeUpdateStepExecutorTest extends AbstractStepExecutorTest
{
    private AttributeUpdateStepExecutor $executor;

    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = self::getServiceByClassName(
            LocalAttributeDefinitionServiceInterface::class
        );
        $attributeGroupService = self::getServiceByClassName(AttributeGroupServiceInterface::class);
        $this->executor = new AttributeUpdateStepExecutor(
            $this->attributeDefinitionService,
            $attributeGroupService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testStepExecution(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $definition = $this->attributeDefinitionService->getAttributeDefinition('foo');

        // Sanity checks
        self::assertSame('dimensions', $definition->getGroup()->getIdentifier());
        self::assertSame('integer', $definition->getType()->getIdentifier());
        self::assertSame(0, $definition->getPosition());
        self::assertSame('Foo', $definition->getName());
        self::assertSame('Description foo', $definition->getDescription());
        self::assertEqualsCanonicalizing([
            'min' => 10,
            'max' => 100,
        ], $definition->getOptions()->all());

        $step = new AttributeUpdateStep(
            new FieldValueCriterion('identifier', 'foo'),
            null,
            'dress_size',
            42,
            [
                'eng-US' => 'bar name',
            ],
            [
                'eng-US' => 'bar description',
            ],
            [
                'min' => 0,
            ],
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $definition = $this->attributeDefinitionService->getAttributeDefinition('foo');
        self::assertSame('dress_size', $definition->getGroup()->getIdentifier());
        self::assertSame('integer', $definition->getType()->getIdentifier());
        self::assertSame(42, $definition->getPosition());
        self::assertSame('bar name', $definition->getName());
        self::assertSame('bar description', $definition->getDescription());
        self::assertSame([
            'min' => 0,
        ], $definition->getOptions()->all());

        $step = new AttributeUpdateStep(
            new FieldValueCriterion('identifier', 'foo'),
            'foo_next',
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        try {
            $this->attributeDefinitionService->getAttributeDefinition('foo');
            self::fail('"foo" Attribute Definition should not be reachable at this point');
        } catch (NotFoundException $e) {
            // ignore
        }

        $definition = $this->attributeDefinitionService->getAttributeDefinition('foo_next');
        self::assertSame('dress_size', $definition->getGroup()->getIdentifier());
        self::assertSame('integer', $definition->getType()->getIdentifier());
        self::assertSame(42, $definition->getPosition());
        self::assertSame('bar name', $definition->getName());
        self::assertSame('bar description', $definition->getDescription());
        self::assertSame([
            'min' => 0,
        ], $definition->getOptions()->all());
    }
}
