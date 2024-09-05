<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStepExecutor
 */
final class AttributeCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private AttributeCreateStepExecutor $executor;

    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = self::getServiceByClassName(
            LocalAttributeDefinitionServiceInterface::class
        );
        $attributeGroupService = self::getServiceByClassName(AttributeGroupServiceInterface::class);
        $attributeTypeService = self::getServiceByClassName(AttributeTypeServiceInterface::class);
        $this->executor = new AttributeCreateStepExecutor(
            $this->attributeDefinitionService,
            $attributeTypeService,
            $attributeGroupService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testStepExecution(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        try {
            $this->attributeDefinitionService->getAttributeDefinition('foo_next');
            self::fail('"foo_next" Attribute Definition should not be reachable at this point');
        } catch (NotFoundException $e) {
            // ignore
        }

        $step = new AttributeCreateStep(
            'foo_next',
            'dress_size',
            'integer',
            42,
            [
                'eng-US' => 'bar name',
            ],
            [
                'eng-US' => 'bar description',
            ],
            [
                'min' => 0,
                'max' => 42,
            ],
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $definition = $this->attributeDefinitionService->getAttributeDefinition('foo_next');
        self::assertSame('dress_size', $definition->getGroup()->getIdentifier());
        self::assertSame('integer', $definition->getType()->getIdentifier());
        self::assertSame(42, $definition->getPosition());
        self::assertSame('bar name', $definition->getName());
        self::assertSame('bar description', $definition->getDescription());
        self::assertEqualsCanonicalizing([
            'min' => 0,
            'max' => 42,
        ], $definition->getOptions()->all());
    }
}
