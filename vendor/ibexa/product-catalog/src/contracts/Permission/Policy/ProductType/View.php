<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class View extends AbstractProductTypePolicy
{
    private const VIEW = 'view';

    private ?ProductTypeInterface $productType;

    public function __construct(?ProductTypeInterface $productType = null)
    {
        $this->productType = $productType;
    }

    public function getFunction(): string
    {
        return self::VIEW;
    }

    public function getObject(): ?ProductTypeInterface
    {
        return $this->productType;
    }
}
