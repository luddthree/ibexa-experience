<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Product;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;

final class Create extends AbstractProductPolicy
{
    private const CREATE = 'create';

    private ?ProductCreateStruct $productCreateStruct;

    public function __construct(?ProductCreateStruct $productCreateStruct = null)
    {
        $this->productCreateStruct = $productCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?ProductCreateStruct
    {
        return $this->productCreateStruct;
    }
}
