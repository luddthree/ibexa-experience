<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class ProductView extends BaseView
{
    private ProductInterface $product;

    private bool $editable;

    public function __construct($templateIdentifier, ProductInterface $product)
    {
        parent::__construct($templateIdentifier);

        $this->product = $product;
        $this->editable = false;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): void
    {
        $this->editable = $editable;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'product' => $this->product,
            'is_editable' => $this->editable,
        ];
    }
}
