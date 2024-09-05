<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class VatCategoryPool implements VatCategoryPoolInterface
{
    /** @var array<string,array<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>> */
    private array $vatCategories;

    /**
     * @param array<string,array<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>> $vatCategories
     */
    public function __construct(array $vatCategories)
    {
        $this->vatCategories = $vatCategories;
    }

    /**
     * @return array<string,\Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface>
     */
    public function getVatCategories(string $region): array
    {
        return $this->vatCategories[$region] ?? [];
    }

    public function getVatCategory(string $region, string $identifier): VatCategoryInterface
    {
        if (isset($this->vatCategories[$region][$identifier])) {
            return $this->vatCategories[$region][$identifier];
        }

        throw new NotFoundException(VatCategoryInterface::class, ['region' => $region, 'identifier' => $identifier]);
    }
}
