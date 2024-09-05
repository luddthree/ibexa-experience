<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class ProductTypeView extends BaseView
{
    private ProductTypeInterface $productType;

    private bool $editable = false;

    /**
     * @param string|\Closure $templateIdentifier Valid path to the template. Can also be a closure.
     */
    public function __construct($templateIdentifier, ProductTypeInterface $productType)
    {
        parent::__construct($templateIdentifier);

        $this->productType = $productType;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }

    public function setProductType(ProductTypeInterface $productType): void
    {
        $this->productType = $productType;
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
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'is_editable' => $this->editable,
            'product_type' => $this->productType,
        ];
    }
}
