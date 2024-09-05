<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder;

/**
 * @internal
 */
final class PriceFieldNameBuilder
{
    /** @var array<string,string|null> */
    private array $context = [
        'currency' => null,
        'customer_group' => 'none',
    ];

    public function __construct(string $currencyCode)
    {
        $this->context['currency'] = strtolower($currencyCode);
    }

    public function withCustomerGroup(string $customerGroupIdentifier): self
    {
        $this->context['customer_group'] = $customerGroupIdentifier;

        return $this;
    }

    public function build(): string
    {
        return 'product_price_' . $this->getContextAsString();
    }

    private function getContextAsString(): string
    {
        $result = [];
        foreach ($this->context as $key => $value) {
            $result[] = $key . '_' . $value;
        }

        return implode('_', $result);
    }
}
