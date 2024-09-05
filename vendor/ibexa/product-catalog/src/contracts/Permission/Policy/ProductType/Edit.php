<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType;

use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\ProductTypeUpdateStruct;

final class Edit extends AbstractProductTypePolicy
{
    private const EDIT = 'edit';

    private ?ProductTypeUpdateStruct $productTypeUpdateStruct;

    public function __construct(?ProductTypeUpdateStruct $productTypeUpdateStruct = null)
    {
        $this->productTypeUpdateStruct = $productTypeUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?ProductTypeUpdateStruct
    {
        return $this->productTypeUpdateStruct;
    }
}
