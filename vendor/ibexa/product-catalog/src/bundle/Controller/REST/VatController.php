<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\VatCategory;
use Ibexa\Bundle\ProductCatalog\REST\Value\VatCategoryList;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;

final class VatController extends RestController
{
    private VatServiceInterface $vatService;

    private RegionServiceInterface $regionService;

    public function __construct(
        VatServiceInterface $vatService,
        RegionServiceInterface $regionService
    ) {
        $this->vatService = $vatService;
        $this->regionService = $regionService;
    }

    public function getVatCategories(string $region): Value
    {
        $restVatCategories = [];
        $vatCategories = $this->vatService->getVatCategories(
            $this->regionService->getRegion($region)
        );

        foreach ($vatCategories as $vatCategory) {
            $restVatCategories[] = new VatCategory($vatCategory);
        }

        return new VatCategoryList($restVatCategories);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getVatCategoryAction(string $region, string $identifier): Value
    {
        $vatCategory = $this->vatService->getVatCategoryByIdentifier(
            $this->regionService->getRegion($region),
            $identifier
        );

        return new VatCategory($vatCategory);
    }
}
