<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency as SpiCurrency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct as SpiCurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct as SpiCurrencyUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\CurrencyList;

final class CurrencyService implements CurrencyServiceInterface
{
    private const CURRENCY_CODE_LIMIT = 3;
    private const VALIDATION_TOO_LONG_CURRENCY_CODE_MESSAGE = 'Currency code cannot be longer than %d characters';
    private const VALIDATION_CURRENCY_CODE_NOT_UNIQUE = 'Currency with code "%s" already exists';

    private HandlerInterface $handler;

    private DomainMapperInterface $domainMapper;

    private PermissionResolverInterface $permissionResolver;

    private CriterionMapper $criterionMapper;

    public function __construct(
        HandlerInterface $handler,
        DomainMapperInterface $domainMapper,
        PermissionResolverInterface $permissionResolver,
        CriterionMapper $criterionMapper
    ) {
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
        $this->domainMapper = $domainMapper;
        $this->criterionMapper = $criterionMapper;
    }

    public function findCurrencies(?CurrencyQuery $query = null): CurrencyListInterface
    {
        $query ??= new CurrencyQuery();
        $criteria = [];

        if ($query->getQuery() !== null) {
            $criteria[] = $this->criterionMapper->handle($query->getQuery());
        }

        $totalCount = $this->handler->countBy($criteria);
        if ($totalCount === 0) {
            return new CurrencyList([], $totalCount);
        }

        $sortBy = [];
        foreach ($query->getSortClauses() as $sortClause) {
            $sortBy[$sortClause->getField()] = $sortClause->getDirection() === AbstractSortClause::SORT_ASC
                ? 'ASC'
                : 'DESC';
        }

        $spiCurrencies = $this->handler->findBy(
            $criteria,
            $sortBy,
            $query->getLimit(),
            $query->getOffset(),
        );

        $apiCurrencies = [];
        foreach ($spiCurrencies as $spiCurrency) {
            $apiCurrencies[] = $this->domainMapper->createFromSpi($spiCurrency);
        }

        return new CurrencyList($apiCurrencies, $totalCount);
    }

    public function getCurrency(int $id): CurrencyInterface
    {
        $spiCurrency = $this->handler->find($id);

        return $this->domainMapper->createFromSpi($spiCurrency);
    }

    public function getCurrencyByCode(string $code): CurrencyInterface
    {
        $spiCurrencies = $this->handler->findBy(['code' => $code], [], 1);
        if (empty($spiCurrencies)) {
            throw new NotFoundException(SpiCurrency::class, $code);
        }

        $spiCurrency = $spiCurrencies[0];

        return $this->domainMapper->createFromSpi($spiCurrency);
    }

    public function createCurrency(CurrencyCreateStruct $struct): CurrencyInterface
    {
        $this->permissionResolver->assertPolicy(new AdministrateCurrencies());

        $code = $struct->getCode();
        $this->validateCurrencyCode($code);

        $spiCreateStruct = new SpiCurrencyCreateStruct();
        $spiCreateStruct->code = $struct->getCode();
        $spiCreateStruct->enabled = $struct->isEnabled();
        $spiCreateStruct->subunits = $struct->getSubunits();

        $spiCurrency = $this->handler->create($spiCreateStruct);

        return $this->domainMapper->createFromSpi($spiCurrency);
    }

    public function updateCurrency(CurrencyInterface $currency, CurrencyUpdateStruct $struct): CurrencyInterface
    {
        $this->permissionResolver->assertPolicy(new AdministrateCurrencies());

        $code = $struct->getCode();
        if ($code !== null) {
            $this->validateCurrencyCode($code, $currency->getId());
        }

        $spiUpdateStruct = new SpiCurrencyUpdateStruct();
        $spiUpdateStruct->id = $currency->getId();
        $spiUpdateStruct->code = $struct->getCode() ?? $currency->getCode();
        $spiUpdateStruct->enabled = $struct->getEnabled() ?? $currency->isEnabled();
        $spiUpdateStruct->subunits = $struct->getSubunits() ?? $currency->getSubUnits();

        $spiCurrency = $this->handler->update($spiUpdateStruct);

        return $this->domainMapper->createFromSpi($spiCurrency);
    }

    public function deleteCurrency(CurrencyInterface $currency): void
    {
        $this->permissionResolver->assertPolicy(new AdministrateCurrencies());

        $loadedCurrency = $this->getCurrency($currency->getId());

        $this->handler->delete($loadedCurrency->getId());
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function validateCurrencyCode(string $code, ?int $id = null): void
    {
        if (mb_strlen($code) > self::CURRENCY_CODE_LIMIT) {
            throw new InvalidArgumentException(
                'struct',
                sprintf(self::VALIDATION_TOO_LONG_CURRENCY_CODE_MESSAGE, self::CURRENCY_CODE_LIMIT)
            );
        }

        try {
            $currency = $this->getCurrencyByCode($code);
            if ($currency->getId() !== $id) {
                throw new InvalidArgumentException(
                    'struct',
                    sprintf(self::VALIDATION_CURRENCY_CODE_NOT_UNIQUE, $code),
                );
            }
        } catch (APINotFoundException $e) {
            // ignore
        }
    }
}
