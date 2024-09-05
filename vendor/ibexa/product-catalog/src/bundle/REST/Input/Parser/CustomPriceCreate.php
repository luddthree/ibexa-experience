<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Money\Currency;
use Money\Money;

/**
 * @template-extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AbstractParser<
 *     \Ibexa\Bundle\ProductCatalog\REST\Value\CustomPriceCreateStruct
 * >
 */
final class CustomPriceCreate extends AbstractParser
{
    private const CUSTOMER_GROUP_KEY = 'customerGroup';
    private const CURRENCY_KEY = 'currency';
    private const AMOUNT_KEY = 'amount';
    private const CUSTOM_AMOUNT_KEY = 'customAmount';

    private CurrencyServiceInterface $currencyService;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        CurrencyServiceInterface $currencyService,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->currencyService = $currencyService;
        $this->customerGroupService = $customerGroupService;
    }

    public function doParse(array $data, ParsingDispatcher $parsingDispatcher): Value\CustomPriceCreateStruct
    {
        $customerGroup = $this->customerGroupService->getCustomerGroupByIdentifier(
            $data[self::CUSTOMER_GROUP_KEY]
        );

        if ($customerGroup === null) {
            throw new NotFoundException(CustomerGroupInterface::class, $data[self::CUSTOMER_GROUP_KEY]);
        }

        $currency = $this->currencyService->getCurrencyByCode(
            $data[self::CURRENCY_KEY]
        );

        $amount = new Money(
            $data[self::AMOUNT_KEY],
            new Currency($currency->getCode())
        );

        $customAmount = new Money(
            $data[self::CUSTOM_AMOUNT_KEY],
            new Currency($currency->getCode())
        );

        return new Value\CustomPriceCreateStruct(
            $customerGroup,
            $currency,
            $amount,
            $customAmount
        );
    }

    public function getRequiredKeys(): array
    {
        return [self::AMOUNT_KEY, self::CURRENCY_KEY, self::CUSTOM_AMOUNT_KEY, self::CUSTOMER_GROUP_KEY];
    }

    public function getIdentifier(): string
    {
        return 'CustomPriceCreate';
    }
}
