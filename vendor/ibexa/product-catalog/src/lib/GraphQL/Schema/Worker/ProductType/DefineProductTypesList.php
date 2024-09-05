<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\ProductType;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Initializer;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class DefineProductTypesList extends Base implements Worker, Initializer
{
    private const PRODUCT_LIST_TYPE = 'ProductTypesList';

    private ProductTypeServiceInterface $productTypeService;

    private ConfigProviderInterface $configProvider;

    public function __construct(
        ProductTypeServiceInterface $productTypeService,
        ConfigProviderInterface $configProvider
    ) {
        $this->productTypeService = $productTypeService;
        $this->configProvider = $configProvider;
    }

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $productType = $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER];

        $schema->addValueToEnum(
            self::PRODUCT_LIST_TYPE,
            new Builder\Input\EnumValue(
                $productType->getIdentifier(),
                ['value' => $this->getNameHelper()->getProductTypeName($productType)]
            )
        );
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface;
    }

    public function init(Builder $schema): void
    {
        if (!$this->isProductTypesListAvailable()) {
            return;
        }

        $schema->addType(
            new Builder\Input\Type(self::PRODUCT_LIST_TYPE, 'enum')
        );
    }

    private function isProductTypesListAvailable(): bool
    {
        if ($this->configProvider->getEngineAlias() === null) {
            return false;
        }

        return $this->productTypeService->findProductTypes()->getTotalCount() > 0;
    }
}
