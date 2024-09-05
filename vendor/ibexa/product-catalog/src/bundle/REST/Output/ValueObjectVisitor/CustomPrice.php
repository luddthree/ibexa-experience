<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\Visitor;

final class CustomPrice extends Price
{
    private const OBJECT_IDENTIFIER = 'CustomPrice';

    private const PRICE_IDENTIFIER_CUSTOM_AMOUNT = 'custom_amount';
    private const PRICE_IDENTIFIER_CUSTOM_MONEY = 'custom_money';
    private const PRICE_IDENTIFIER_CUSTOM_RULE = 'custom_rule';

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface $data
     */
    protected function visitProperties(Visitor $visitor, Generator $generator, PriceInterface $data): void
    {
        parent::visitProperties($visitor, $generator, $data);

        $generator->valueElement(
            self::PRICE_IDENTIFIER_CUSTOM_MONEY,
            $data->getCustomPrice() ? $data->getCustomPrice()->getAmount() : null
        );
        $generator->valueElement(self::PRICE_IDENTIFIER_CUSTOM_AMOUNT, $data->getCustomPriceAmount());
        $generator->valueElement(self::PRICE_IDENTIFIER_CUSTOM_RULE, $data->getCustomPriceRule());
    }

    protected function getObjectIdentifier(): string
    {
        return self::OBJECT_IDENTIFIER;
    }
}
