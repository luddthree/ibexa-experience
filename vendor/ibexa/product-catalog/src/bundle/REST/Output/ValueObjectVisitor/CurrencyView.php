<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Currency as RestCurrency;
use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyList as RestCurrencyList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class CurrencyView extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'CurrencyView';
    private const VIEW_IDENTIFIER = 'identifier';
    private const CURRENCY_QUERY_IDENTIFIER = 'CurrencyQuery';
    private const RESULT_IDENTIFIER = 'Result';

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyView $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $restCurrencies = [];
        $viewIdentifier = $data->getIdentifier();
        $currencyList = $data->getCurrencyList();

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::VIEW_IDENTIFIER, $viewIdentifier);
        $generator->startObjectElement(self::CURRENCY_QUERY_IDENTIFIER);
        $generator->endObjectElement(self::CURRENCY_QUERY_IDENTIFIER);

        $generator->startObjectElement(self::RESULT_IDENTIFIER, 'ViewResult');
        $generator->valueElement('count', $currencyList->getTotalCount());

        foreach ($currencyList->getCurrencies() as $currency) {
            $restCurrencies[] = new RestCurrency($currency);
        }

        $visitor->visitValueObject(new RestCurrencyList($restCurrencies));

        $generator->endObjectElement(self::RESULT_IDENTIFIER);
        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
