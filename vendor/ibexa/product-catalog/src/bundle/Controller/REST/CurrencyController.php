<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\Currency;
use Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyList;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class CurrencyController extends RestController
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function listCurrenciesAction(): Value
    {
        $restCurrencies = [];
        $query = new CurrencyQuery(null, [], null);
        $currencies = $this->currencyService->findCurrencies($query);

        foreach ($currencies as $currency) {
            $restCurrencies[] = new Currency($currency);
        }

        return new CurrencyList($restCurrencies);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getCurrencyAction(int $id): Value
    {
        $currency = $this->currencyService->getCurrency($id);

        return new Currency($currency);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createCurrencyAction(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $currencyCreateStruct = new CurrencyCreateStruct(
            $input->getCode(),
            $input->getSubunits(),
            $input->isEnabled()
        );

        $currency = $this->currencyService->createCurrency($currencyCreateStruct);

        return new Currency($currency);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateCurrencyAction(Request $request, int $id): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\CurrencyUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $currency = $this->currencyService->getCurrency($id);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage());
        }

        $currencyUpdateStruct = new CurrencyUpdateStruct();
        $currencyUpdateStruct->setCode($input->getCode());
        $currencyUpdateStruct->setSubunits($input->getSubUnits());
        $currencyUpdateStruct->setEnabled($input->isEnabled());

        $currency = $this->currencyService->updateCurrency($currency, $currencyUpdateStruct);

        return new Currency($currency);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteCurrencyAction(int $id): Value
    {
        $currency = $this->currencyService->getCurrency($id);
        $this->currencyService->deleteCurrency($currency);

        return new NoContent();
    }
}
