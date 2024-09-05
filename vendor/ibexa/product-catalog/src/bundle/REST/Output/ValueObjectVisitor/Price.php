<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Currency;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class Price extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Price';

    private const PRICE_IDENTIFIER_ID = 'id';
    private const PRICE_IDENTIFIER_AMOUNT = 'amount';
    private const PRICE_IDENTIFIER_BASE_AMOUNT = 'base_amount';
    private const PRICE_IDENTIFIER_MONEY = 'money';
    private const PRICE_IDENTIFIER_BASE_MONEY = 'base_money';

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\PriceInterface $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement($this->getObjectIdentifier());
        $visitor->setHeader('Content-Type', $generator->getMediaType($this->getObjectIdentifier()));

        $this->visitProperties($visitor, $generator, $data);

        $generator->endObjectElement($this->getObjectIdentifier());
    }

    protected function visitProperties(Visitor $visitor, Generator $generator, PriceInterface $data): void
    {
        $generator->valueElement(self::PRICE_IDENTIFIER_ID, $data->getId());

        $generator->valueElement(self::PRICE_IDENTIFIER_AMOUNT, $data->getAmount());
        $generator->valueElement(self::PRICE_IDENTIFIER_BASE_AMOUNT, $data->getBaseAmount());

        $visitor->visitValueObject(new Currency($data->getCurrency()));

        $generator->valueElement(self::PRICE_IDENTIFIER_MONEY, $data->getMoney()->getAmount());
        $generator->valueElement(self::PRICE_IDENTIFIER_BASE_MONEY, $data->getBaseMoney()->getAmount());
    }

    protected function getObjectIdentifier(): string
    {
        return self::OBJECT_IDENTIFIER;
    }
}
