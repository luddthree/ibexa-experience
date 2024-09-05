<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class ProductTypeSettingCreateStruct
{
    private int $fieldDefinitionId;

    private int $status;

    private bool $isVirtual;

    public function __construct(
        int $fieldDefinitionId,
        int $status,
        bool $isVirtual
    ) {
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->status = $status;
        $this->isVirtual = $isVirtual;
    }

    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
