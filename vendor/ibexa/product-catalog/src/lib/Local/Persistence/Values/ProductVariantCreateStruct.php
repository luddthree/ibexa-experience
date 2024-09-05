<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class ProductVariantCreateStruct
{
    public string $baseProductCode;

    public string $code;

    public int $fieldId;

    public function __construct(string $baseProductCode, int $fieldId, string $code)
    {
        $this->baseProductCode = $baseProductCode;
        $this->code = $code;
        $this->fieldId = $fieldId;
    }
}
