<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType;

use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeCreateStruct;

final class Create extends AbstractProductTypePolicy
{
    private const CREATE = 'create';

    private ?ProductTypeCreateStruct $productTypeCreateStruct;

    public function __construct(?ProductTypeCreateStruct $productTypeCreateStruct = null)
    {
        $this->productTypeCreateStruct = $productTypeCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?ProductTypeCreateStruct
    {
        return $this->productTypeCreateStruct;
    }
}
