<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Product;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;

final class Edit extends AbstractProductPolicy
{
    private const EDIT = 'edit';

    private ?ProductUpdateStruct $productUpdateStruct;

    public function __construct(?ProductUpdateStruct $productUpdateStruct = null)
    {
        $this->productUpdateStruct = $productUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?ProductUpdateStruct
    {
        return $this->productUpdateStruct;
    }
}
