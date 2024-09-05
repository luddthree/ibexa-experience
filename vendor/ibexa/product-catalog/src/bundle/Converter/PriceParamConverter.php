<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;

final class PriceParamConverter extends RepositoryParamConverter
{
    private ProductPriceServiceInterface $priceService;

    public function __construct(ProductPriceServiceInterface $priceService)
    {
        $this->priceService = $priceService;
    }

    protected function getSupportedClass(): string
    {
        return PriceInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'priceId';
    }

    /**
     * @param string|int $id
     */
    protected function loadValueObject($id): PriceInterface
    {
        return $this->priceService->getPriceById((int) $id);
    }
}
