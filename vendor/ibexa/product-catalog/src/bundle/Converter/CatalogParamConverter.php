<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;

final class CatalogParamConverter extends RepositoryParamConverter
{
    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /** @phpstan-return class-string */
    protected function getSupportedClass(): string
    {
        return CatalogInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'catalogId';
    }

    /**
     * @param string $id
     */
    protected function loadValueObject($id): CatalogInterface
    {
        return $this->catalogService->getCatalog((int) $id);
    }
}
