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

final class PriceList extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'PriceList';

    private const PRICE_LIST_IDENTIFIER_PRICES = 'prices';
    private const PRICE_LIST_IDENTIFIER_TOTAL_COUNT = 'totalCount';

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\PriceList $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->startList(self::PRICE_LIST_IDENTIFIER_PRICES);
        foreach ($data->getPrices() as $price) {
            $visitor->visitValueObject($price);
        }
        $generator->endList(self::PRICE_LIST_IDENTIFIER_PRICES);

        $generator->valueElement(self::PRICE_LIST_IDENTIFIER_TOTAL_COUNT, $data->getTotalCount());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
