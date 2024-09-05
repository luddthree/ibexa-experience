<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

final class AttributeGroupCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->attributeGroupService = self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
        $this->executor = new AttributeGroupCreateStepExecutor($this->attributeGroupService);
        $this->configureExecutor($this->executor);
        self::setAdministratorUser();
    }

    public function testExecution(): void
    {
        $step = new AttributeGroupCreateStep(
            'foo_dimensions',
            [
                'eng-US' => 'Is it really another Dimension?',
            ],
            42,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $group = $this->attributeGroupService->getAttributeGroup('foo_dimensions');
        self::assertSame('foo_dimensions', $group->getIdentifier());
        self::assertSame('Is it really another Dimension?', $group->getName());
        self::assertSame(42, $group->getPosition());
    }
}
