<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct as AttributeDefinitionUpdateStructValue;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Edit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\ProductCatalog\Local\Permission\Context;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Tests\ProductCatalog\Local\Permission\ContextProvider\AbstractContextProviderTest;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextProvider\AttributeDefinition\AttributeDefinitionUpdateStruct
 */
final class AttributeDefinitionUpdateStructTest extends AbstractContextProviderTest
{
    private AttributeDefinitionUpdateStructValue $object;

    public function setUp(): void
    {
        $this->object = new AttributeDefinitionUpdateStructValue();
    }

    protected function getContextProvider(): ContextProviderInterface
    {
        return new AttributeDefinitionUpdateStruct();
    }

    protected function getPolicy(): PolicyInterface
    {
        return new Edit($this->object);
    }

    protected function getUnsupportedPolicy(): PolicyInterface
    {
        return new Edit();
    }

    protected function getExpectedContext(): ContextInterface
    {
        return new Context($this->object);
    }
}
