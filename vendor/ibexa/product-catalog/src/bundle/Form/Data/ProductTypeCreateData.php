<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductTypeCreateData
{
    /**
     * @Assert\NotBlank()
     */
    private ?string $type;

    public function __construct(?string $type = null)
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
