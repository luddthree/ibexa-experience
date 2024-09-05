<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\Product;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class View extends AbstractProductPolicy
{
    private const VIEW = 'view';

    private ?ProductInterface $product;

    public function __construct(?ProductInterface $product = null)
    {
        $this->product = $product;
    }

    public function getFunction(): string
    {
        return self::VIEW;
    }

    public function getObject(): ?ProductInterface
    {
        return $this->product;
    }
}
