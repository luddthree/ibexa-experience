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

final class CatalogList extends ValueObjectVisitor
{
    private const LIST_OBJECT_IDENTIFIER = 'CatalogList';
    private const OBJECT_IDENTIFIER = 'Catalog';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CatalogList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::LIST_OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::LIST_OBJECT_IDENTIFIER));

        $generator->startList(self::OBJECT_IDENTIFIER);

        foreach ($data->catalogs as $catalog) {
            $visitor->visitValueObject($catalog);
        }

        $generator->endList(self::OBJECT_IDENTIFIER);
        $generator->endObjectElement(self::LIST_OBJECT_IDENTIFIER);
    }
}
