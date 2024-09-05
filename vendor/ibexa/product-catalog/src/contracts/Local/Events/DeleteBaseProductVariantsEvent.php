<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class DeleteBaseProductVariantsEvent extends AfterEvent
{
    private ProductInterface $baseProduct;

    /** @var string[] */
    private array $deletedVariantCodes;

    /**
     * @param string[] $deletedVariantCodes
     */
    public function __construct(ProductInterface $baseProduct, array $deletedVariantCodes = [])
    {
        $this->baseProduct = $baseProduct;
        $this->deletedVariantCodes = $deletedVariantCodes;
    }

    public function getBaseProduct(): ProductInterface
    {
        return $this->baseProduct;
    }

    /**
     * @return string[]
     */
    public function getDeletedVariantCodes(): array
    {
        return $this->deletedVariantCodes;
    }

    /**
     * @param string[] $deletedVariantCodes
     */
    public function setDeletedVariantCodes(array $deletedVariantCodes): void
    {
        $this->deletedVariantCodes = $deletedVariantCodes;
    }
}
