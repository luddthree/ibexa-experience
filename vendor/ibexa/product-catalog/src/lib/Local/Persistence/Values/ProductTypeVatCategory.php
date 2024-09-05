<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class ProductTypeVatCategory
{
    private int $id;

    private int $fieldDefinitionId;

    private string $region;

    private string $vatCategory;

    public function __construct(
        int $id,
        int $fieldDefinitionId,
        string $regionIdentifier,
        string $vatCategoryIdentifier
    ) {
        $this->id = $id;
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->region = $regionIdentifier;
        $this->vatCategory = $vatCategoryIdentifier;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getVatCategory(): string
    {
        return $this->vatCategory;
    }
}
