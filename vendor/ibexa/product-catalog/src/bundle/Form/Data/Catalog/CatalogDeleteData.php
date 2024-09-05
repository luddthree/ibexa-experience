<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CatalogDeleteData
{
    /**
     * @Assert\NotBlank()
     */
    private ?CatalogInterface $catalog;

    public function __construct(?CatalogInterface $catalog = null)
    {
        $this->catalog = $catalog;
    }

    public function getCatalog(): ?CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(?CatalogInterface $catalog): void
    {
        $this->catalog = $catalog;
    }
}
