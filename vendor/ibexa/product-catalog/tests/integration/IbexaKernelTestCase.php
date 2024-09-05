<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase as BaseIbexaKernelTestCase;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Migration\Repository\Migration;
use Symfony\Component\Serializer\SerializerInterface;

abstract class IbexaKernelTestCase extends BaseIbexaKernelTestCase
{
    use KernelCommonTestTrait;

    protected static function getAssetsService(): AssetServiceInterface
    {
        return self::getServiceByClassName(AssetServiceInterface::class);
    }

    protected static function getLocalAssetService(): LocalAssetServiceInterface
    {
        return self::getServiceByClassName(LocalAssetServiceInterface::class);
    }

    protected static function getCurrencyService(): CurrencyServiceInterface
    {
        return self::getServiceByClassName(CurrencyServiceInterface::class);
    }

    protected static function getCustomerGroupService(): CustomerGroupServiceInterface
    {
        return self::getServiceByClassName(CustomerGroupServiceInterface::class);
    }

    protected static function getProductService(): ProductServiceInterface
    {
        return self::getServiceByClassName(ProductServiceInterface::class);
    }

    protected static function getLocalProductService(): LocalProductServiceInterface
    {
        return self::getServiceByClassName(LocalProductServiceInterface::class);
    }

    protected static function getProductTypeService(): ProductTypeServiceInterface
    {
        return self::getServiceByClassName(ProductTypeServiceInterface::class);
    }

    protected static function getLocalProductTypeService(): LocalProductTypeServiceInterface
    {
        return self::getServiceByClassName(LocalProductTypeServiceInterface::class);
    }

    protected static function getProductPriceService(): ProductPriceServiceInterface
    {
        return self::getServiceByClassName(ProductPriceServiceInterface::class);
    }

    protected static function getAttributeGroupService(): AttributeGroupServiceInterface
    {
        return self::getServiceByClassName(AttributeGroupServiceInterface::class);
    }

    protected static function getLocalAttributeGroupService(): LocalAttributeGroupServiceInterface
    {
        return self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
    }

    protected static function getProductAvailabilityService(): ProductAvailabilityServiceInterface
    {
        return self::getServiceByClassName(ProductAvailabilityServiceInterface::class);
    }

    protected static function getCatalogService(): CatalogServiceInterface
    {
        return self::getServiceByClassName(CatalogServiceInterface::class);
    }

    protected static function getAttributeDefinitionService(): AttributeDefinitionServiceInterface
    {
        return self::getServiceByClassName(AttributeDefinitionServiceInterface::class);
    }

    protected static function getLocalAttributeDefinitionService(): LocalAttributeDefinitionServiceInterface
    {
        return self::getServiceByClassName(LocalAttributeDefinitionServiceInterface::class);
    }

    protected static function getLanguageResolver(): LanguageResolver
    {
        return self::getServiceByClassName(LanguageResolver::class);
    }

    protected static function getMigrationSerializer(): SerializerInterface
    {
        return self::getServiceByClassName(SerializerInterface::class, 'ibexa.migrations.serializer');
    }

    protected static function getFieldHelper(): FieldHelper
    {
        return self::getServiceByClassName(FieldHelper::class);
    }

    protected static function assertContentTypeExists(string $identifier): void
    {
        try {
            $contentTypeService = self::getContentTypeService();
            $contentTypeService->loadContentTypeByIdentifier($identifier);
            $found = true;
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertTrue($found, sprintf('ContentType with identifier "%s" does not exist.', $identifier));
    }

    protected function executeMigration(string $name): void
    {
        $path = __DIR__ . '/_migrations/' . $name;

        $content = file_get_contents($path);
        if ($content !== false) {
            $migrationService = self::getContainer()->get(MigrationService::class);
            $migrationService->executeOne(new Migration(uniqid(), $content));
        } else {
            self::fail(sprintf('Unable to load "%s" fixture', $path));
        }
    }
}
