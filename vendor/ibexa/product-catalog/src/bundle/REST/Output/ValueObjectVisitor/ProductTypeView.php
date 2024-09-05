<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductType as RestProductType;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeList as RestProductTypeList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductTypeView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ProductTypeView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const PRODUCT_TYPE_QUERY_IDENTIFIER = 'ProductTypeQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restProductTypes = [];
        $viewIdentifier = $data->getIdentifier();
        $productTypeList = $data->getProductTypeList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::PRODUCT_TYPE_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::PRODUCT_TYPE_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $productTypeList->getTotalCount());

        foreach ($productTypeList->getProductTypes() as $productType) {
            $restProductTypes[] = new RestProductType($productType);
        }

        $visitor->visitValueObject(new RestProductTypeList($restProductTypes));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
