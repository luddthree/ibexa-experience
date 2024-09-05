<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;

final class AttributeGroupUpdateData extends AbstractAttributeGroupData
{
    private ?string $originalIdentifier;

    public function __construct(
        ?string $originalIdentifier = null,
        ?string $identifier = null,
        ?string $name = null,
        ?int $position = null,
        ?Language $language = null
    ) {
        parent::__construct($identifier, $name, $language, $position);

        $this->originalIdentifier = $originalIdentifier;
    }

    public function getOriginalIdentifier(): ?string
    {
        return $this->originalIdentifier;
    }

    public function setOriginalIdentifier(?string $originalIdentifier): void
    {
        $this->originalIdentifier = $originalIdentifier;
    }
}
