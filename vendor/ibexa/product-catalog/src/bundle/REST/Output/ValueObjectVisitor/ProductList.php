<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class ProductList extends ValueObjectVisitor
{
    private const LIST_OBJECT_IDENTIFIER = 'ProductList';
    private const OBJECT_IDENTIFIER = 'Product';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\ProductList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::LIST_OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::LIST_OBJECT_IDENTIFIER));

        $generator->startList(self::OBJECT_IDENTIFIER);

        foreach ($data->products as $product) {
            $visitor->visitValueObject($product);
        }

        $generator->endList(self::OBJECT_IDENTIFIER);
        $generator->endObjectElement(self::LIST_OBJECT_IDENTIFIER);
    }
}
