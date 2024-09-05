<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy;

use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values;
use Ibexa\ProductCatalog\Local\Persistence;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class DoctrineSchemaMetadataRegistryTest extends IbexaKernelTestCase
{
    private DoctrineSchemaMetadataRegistryInterface $registry;

    protected function setUp(): void
    {
        $this->registry = self::getServiceByClassName(DoctrineSchemaMetadataRegistryInterface::class);
    }

    public function testAllAvailableMetadata(): void
    {
        $availableMetadata = $this->registry->getAvailableMetadata();

        self::assertEqualsCanonicalizing([
            Values\Asset\AssetInterface::class,
            Values\AttributeInterface::class,
            Values\AttributeDefinitionInterface::class,
            Values\AttributeGroupInterface::class,
            Values\CustomerGroupInterface::class,
            Values\CatalogInterface::class,
            Values\CurrencyInterface::class,
            Values\ProductInterface::class,
            Persistence\Values\Product::class,
            Values\PriceInterface::class,
            Values\Availability\AvailabilityInterface::class,
            Values\AttributeDefinitionAssignmentInterface::class,
            Persistence\Values\ProductTypeVatCategory::class,
            Persistence\Values\ProductTypeSetting::class,
        ], $availableMetadata);
    }
}
