<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\AttributeGroupView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;

final class AttributeGroupViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $view = new AttributeGroupView(
            'example.html.twig',
            $attributeGroup,
        );
        $view->setEditable(true);

        self::assertEquals([
            'is_editable' => true,
            'attribute_group' => $attributeGroup,
        ], $view->getParameters());
    }
}
