<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Catalog as RestCatalog;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogList as RestCatalogList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class CatalogView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CatalogView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const PRODUCT_QUERY_IDENTIFIER = 'CatalogQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $viewIdentifier = $data->getIdentifier();
        $catalogList = $data->getCatalogList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::PRODUCT_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::PRODUCT_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $catalogList->count());

        $restCatalogs = [];
        foreach ($catalogList->getCatalogs() as $catalog) {
            $restCatalogs[] = new RestCatalog($catalog);
        }

        $visitor->visitValueObject(new RestCatalogList($restCatalogs));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
