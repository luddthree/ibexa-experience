<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;
use Ibexa\Bundle\ProductCatalog\REST\Value\Product;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductVariant extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductVariant';
    private const VARIANT_CODE = 'code';
    private const VARIANT_NAME = 'name';
    private const DISCRIMINATOR_ATTRIBUTES_LIST_OBJECT_IDENTIFIER = 'DiscriminatorAttributesList';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariant $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $productVariant = $data->productVariant;
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VARIANT_CODE, $productVariant->getCode());
        $generator->valueElement(self::VARIANT_NAME, $productVariant->getName());

        $restBaseProduct = new Product($productVariant->getBaseProduct());
        $visitor->visitValueObject($restBaseProduct);

        $generator->startList(self::DISCRIMINATOR_ATTRIBUTES_LIST_OBJECT_IDENTIFIER);

        foreach ($productVariant->getAttributes() as $attribute) {
            $restAttribute = new Attribute($attribute->getAttributeDefinition(), $attribute->getValue());
            $visitor->visitValueObject($restAttribute);
        }

        $generator->endList(self::DISCRIMINATOR_ATTRIBUTES_LIST_OBJECT_IDENTIFIER);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
