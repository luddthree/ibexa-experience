<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Catalog;

final class CatalogTransitionData
{
    private int $id;

    private ?string $transition;

    public function __construct(
        int $id,
        ?string $transition = null
    ) {
        $this->id = $id;
        $this->transition = $transition;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTransition(): ?string
    {
        return $this->transition;
    }

    public function setTransition(?string $transition): void
    {
        $this->transition = $transition;
    }
}
