<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseProductAvailabilityServiceTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage('eng-US');
        self::setAdministratorUser();
    }

    protected function getProductServiceInstance(): LocalProductServiceInterface
    {
        $productService = self::getServiceByClassName(LocalProductServiceInterface::class);

        if (!$productService instanceof LocalProductServiceInterface) {
            self::markTestSkipped(
                'Product service data modification operations are not supported by ' . get_class($productService)
            );
        }

        return $productService;
    }

    protected function createProductAvailability(
        bool $availability,
        bool $isInfinite,
        ?int $stock
    ): AvailabilityInterface {
        $product = $this->getProductServiceInstance()->getProduct('0001');

        $createStruct = new ProductAvailabilityCreateStruct(
            $product,
            $availability,
            $isInfinite,
            $stock
        );

        return self::getProductAvailabilityService()->createProductAvailability($createStruct);
    }
}
