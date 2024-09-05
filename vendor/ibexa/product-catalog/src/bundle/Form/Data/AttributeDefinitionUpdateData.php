<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class AttributeDefinitionUpdateData extends AbstractAttributeDefinitionData
{
    private ?string $originalIdentifier;

    public function __construct(
        ?string $originalIdentifier = null,
        ?string $identifier = null,
        ?AttributeGroupInterface $attributeGroup = null,
        ?string $name = null,
        ?string $description = null,
        ?Language $language = null,
        ?int $position = null,
        array $options = []
    ) {
        parent::__construct($identifier, $attributeGroup, $name, $description, $language, $position, $options);

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
