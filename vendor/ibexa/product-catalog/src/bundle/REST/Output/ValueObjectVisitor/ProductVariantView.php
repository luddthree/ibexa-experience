<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariant as RestProductVariant;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantList as RestProductVariantList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductVariantView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductVariantView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const PRODUCT_QUERY_IDENTIFIER = 'ProductVariantQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restProductVariants = [];
        $productVariantList = $data->getProductVariantList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $data->getIdentifier());

        $generator->startObjectElement(self::PRODUCT_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::PRODUCT_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $productVariantList->count());

        foreach ($productVariantList->getVariants() as $variant) {
            $restProductVariants[] = new RestProductVariant($variant);
        }

        $visitor->visitValueObject(new RestProductVariantList($restProductVariants));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
