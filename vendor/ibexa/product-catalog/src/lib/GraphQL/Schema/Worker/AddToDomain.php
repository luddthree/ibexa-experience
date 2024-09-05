<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;

final class AddToDomain extends Base implements Worker
{
    private const DOMAIN_IDENTIFIER = 'Domain';

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
        $schema->addType(
            new Builder\Input\Type(
                self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
                'object'
            )
        );

        $schema->addFieldToType(
            self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
            new Builder\Input\Field(
                '_types',
                self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER,
                ['resolve' => []]
            )
        );

        $schema->addFieldToType(
            self::DOMAIN_IDENTIFIER,
            new Builder\Input\Field(
                'products',
                self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
                ['resolve' => []]
            )
        );
    }

    /**
     * @param array<mixed> $args
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            !$schema->hasType(self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER) &&
            $schema->hasType(self::DOMAIN_IDENTIFIER) &&
            $this->configProvider->getEngineAlias() !== null &&
            $this->productTypeService->findProductTypes()->getTotalCount() > 0;
    }
}
