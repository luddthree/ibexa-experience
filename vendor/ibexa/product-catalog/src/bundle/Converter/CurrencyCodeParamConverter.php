<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;

final class CurrencyCodeParamConverter extends RepositoryParamConverter
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    protected function getSupportedClass(): string
    {
        return CurrencyInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'currencyCode';
    }

    /**
     * @param string|int $id
     */
    protected function loadValueObject($id): CurrencyInterface
    {
        $code = (string) $id;

        return $this->currencyService->getCurrencyByCode($code);
    }
}
