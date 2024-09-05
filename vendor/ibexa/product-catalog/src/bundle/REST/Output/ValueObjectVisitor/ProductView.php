<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Product as RestProduct;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductList as RestProductList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const PRODUCT_QUERY_IDENTIFIER = 'ProductQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restProducts = [];
        $viewIdentifier = $data->getIdentifier();
        $productList = $data->getProductList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::PRODUCT_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::PRODUCT_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $productList->count());

        foreach ($productList->getProducts() as $product) {
            $restProducts[] = new RestProduct($product);
        }

        $visitor->visitValueObject(new RestProductList($restProducts));

        $aggregations = $productList->getAggregations();
        if ($aggregations !== null && count($aggregations) > 0) {
            $generator->startList('aggregations');
            foreach ($aggregations as $aggregationResult) {
                $visitor->visitValueObject($aggregationResult);
            }
            $generator->endList('aggregations');
        }

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
