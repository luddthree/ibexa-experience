<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker;

use Ibexa\ProductCatalog\GraphQL\Schema\NameHelper;

abstract class Base
{
    public const DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER = 'DomainProductTypes';
    public const DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER = 'DomainProducts';
    public const PRODUCT_TYPE_DEFINITION_IDENTIFIER = 'ProductType';
    public const PRODUCT_DEFINITION_IDENTIFIER = 'Product';
    public const ATTRIBUTE_DEFINITION_IDENTIFIER = 'Attribute';
    public const FIELD_DEFINITION_IDENTIFIER = 'FieldDefinition';
    public const BASE_PRODUCT_CONNECTION_TYPE = 'BaseProductConnection';
    public const BASE_PRODUCT_TYPE = 'BaseProduct';

    private NameHelper $nameHelper;

    public function setNameHelper(NameHelper $nameHelper): void
    {
        $this->nameHelper = $nameHelper;
    }

    protected function getNameHelper(): NameHelper
    {
        return $this->nameHelper;
    }
}
