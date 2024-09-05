<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStepExecutor
 */
final class AttributeDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private AttributeDeleteStepExecutor $executor;

    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = self::getServiceByClassName(
            LocalAttributeDefinitionServiceInterface::class
        );
        $this->executor = new AttributeDeleteStepExecutor(
            $this->attributeDefinitionService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testStepExecution(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $this->attributeDefinitionService->getAttributeDefinition('empty');

        $step = new AttributeDeleteStep(
            new FieldValueCriterion('identifier', 'empty'),
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        try {
            $this->attributeDefinitionService->getAttributeDefinition('empty');
            self::fail('"empty" Attribute Definition should not be reachable at this point');
        } catch (NotFoundException $e) {
            // ignore
        }
    }
}
