<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class ProductTypeVatCategoryCreateStruct
{
    private int $fieldDefinitionId;

    private int $status;

    private string $region;

    private string $vatCategory;

    public function __construct(int $fieldDefinitionId, int $status, string $region, string $vatCategory)
    {
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->status = $status;
        $this->region = $region;
        $this->vatCategory = $vatCategory;
    }

    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    public function getStatus(): int
    {
        return $this->status;
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
