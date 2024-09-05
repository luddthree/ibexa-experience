<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Rest\Value;

final class CurrencyView extends Value
{
    private string $identifier;

    private CurrencyListInterface $currencyList;

    public function __construct(string $identifier, CurrencyListInterface $currencyList)
    {
        $this->identifier = $identifier;
        $this->currencyList = $currencyList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCurrencyList(): CurrencyListInterface
    {
        return $this->currencyList;
    }
}
