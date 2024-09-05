<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueCatalogIdentifier;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueCatalogIdentifier
 */
final class CatalogCopyData
{
    private ?CatalogInterface $catalog;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=64)
     * @Assert\Regex("/^\w+$/")
     */
    private ?string $identifier;

    public function __construct(
        ?CatalogInterface $catalog = null,
        ?string $identifier = null
    ) {
        $this->catalog = $catalog;
        $this->identifier = $identifier;
    }

    public function getCatalog(): ?CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): self
    {
        $this->catalog = $catalog;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }
}
