<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Events;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
final class ProductCodeChangedEvent extends Event
{
    private ProductInterface $product;

    private string $oldCode;

    private string $newCode;

    public function __construct(
        ProductInterface $product,
        string $oldCode,
        string $newCode
    ) {
        $this->product = $product;
        $this->oldCode = $oldCode;
        $this->newCode = $newCode;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getOldCode(): string
    {
        return $this->oldCode;
    }

    public function getNewCode(): string
    {
        return $this->newCode;
    }
}
