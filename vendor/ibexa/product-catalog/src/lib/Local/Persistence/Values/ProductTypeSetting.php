<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class ProductTypeSetting
{
    private int $id;

    private int $fieldDefinitionId;

    private bool $isVirtual;

    public function __construct(
        int $id,
        int $fieldDefinitionId,
        bool $isVirtual
    ) {
        $this->id = $id;
        $this->fieldDefinitionId = $fieldDefinitionId;
        $this->isVirtual = $isVirtual;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFieldDefinitionId(): int
    {
        return $this->fieldDefinitionId;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }
}
